<?php

namespace SauvabelinBundle\Command;

use Doctrine\ORM\EntityManager;
use NetBS\FichierBundle\Entity\Famille;
use Ovesco\FacturationBundle\Entity\Compte;
use Ovesco\FacturationBundle\Entity\Creance;
use Ovesco\FacturationBundle\Entity\Facture;
use Ovesco\FacturationBundle\Entity\Paiement;
use Ovesco\FacturationBundle\Entity\Rappel;
use SauvabelinBundle\Entity\BSMembre;
use SauvabelinBundle\Import\Importator;
use SauvabelinBundle\Import\Model\WNGFacture;
use SauvabelinBundle\Import\Model\WNGHelper;
use SauvabelinBundle\Import\Model\WNGMembre;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportFacturesCommand extends ContainerAwareCommand
{
    protected $compte;

    protected $wngMembres;

    protected $wngFamilles;

    /** @var WNGFacture[] */
    protected $notFound = [];

    /** @var Famille[] */
    protected $familles = [];

    /** @var EntityManager */
    protected $manager;

    protected $io;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sauvabelin:import:factures')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $this->manager = $manager;

        $this->familles = $this->manager->getRepository('NetBSFichierBundle:Famille')->findAll();
        $io = new SymfonyStyle($input, $output);
        $this->io = $io;

        $compte = new Compte();
        $compte->setCcp("01-066840-7");
        $compte->setIban("01-066840-7");
        $compte->setLine1("Brigade de Sauvabelin");
        $compte->setLine2("Case Postale 5455");
        $compte->setLine3("1002 Lausanne");

        $manager->persist($compte);
        $this->compte = $compte;

        $importator = new Importator(
            $this->getContainer()->getParameter('database_host'),
            $this->getContainer()->getParameter('database_name'),
            $this->getContainer()->getParameter('database_user'),
            $this->getContainer()->getParameter('database_password')
        );

        $WNGFacturs = $importator->loadFactures();
        $this->wngMembres = $importator->loadMembres();
        $this->wngFamilles = $importator->loadFamilles();
        $io->progressStart(count($WNGFacturs));

        foreach($WNGFacturs as $WNGFacture) {

            if ($WNGFacture->idMembre === 0 && $WNGFacture->idFamille === 0) {
                $this->notFound[] = $WNGFacture;
                continue;
            }

            $debiteur = $WNGFacture->idMembre > 0 ? $this->getMembre($WNGFacture, $WNGFacture->idMembre) : $this->getFamille($WNGFacture);
            if (!$debiteur) {
                continue;
            }

            $creance = $this->toCreance($WNGFacture);
            $creance->setDebiteur($debiteur);
            $manager->persist($creance);

            //if($WNGFacture->statusFacture !== 'non_payee') {
                $facture = $this->toFacture($WNGFacture);
                $facture->setDebiteur($debiteur);
                $facture->addCreance($creance);
                $manager->persist($facture);
            //}
            $io->progressAdvance();
        }

        $io->progressFinish();
        $manager->flush();
        dump(count($this->notFound));

        /** @var WNGFacture $unpaid */
        $unpaid = array_filter($this->notFound, function(WNGFacture $facture) {
            return $facture->statusFacture !== 'payee';
        });

        foreach($unpaid as $item)
            dump(explode(' ', $item->remarques));

        $str = array_map(function(WNGFacture $facture) {
            return $facture->idFacture . ";" . $facture->idMembre . ";" . $facture->idFamille . ";" . $facture->statusFacture . ";"
                . $facture->montantFacture . ";" . $facture->montantPayeFacture . ";" . $facture->rabaisFacture . ";" . $facture->nomFacture
                . ";" . $facture->remarques . "\n";
        }, $this->notFound);

        file_put_contents(__DIR__ . "/" . time() . ".csv", $str);
    }

    /**
     * @param $idMembre
     * @return BSMembre|null
     */
    private function getMembre(WNGFacture $facture, $idMembre) {

        $WNGMembre = isset($this->wngMembres[$idMembre]) ? $this->wngMembres[$idMembre] : null;

        if(!$WNGMembre) {
            $this->io->writeln("Membre not found.");
            $this->notFound[] = $facture;
            return;
        }

        $numeroBs   = $WNGMembre->numeroMembre;
        if(empty($numeroBs)) {
            $this->io->writeln("Got empty numero BS for membre {$WNGMembre->prenom} {$WNGMembre->nom}");
            $this->notFound[] = $facture;
            return;
        }

        $netBSMembre = $this->manager->getRepository('SauvabelinBundle:BSMembre')->findBy(['numeroBS' => $numeroBs]);
        $amount = count($netBSMembre);

        if($amount !== 1) {
            $this->io->writeln("Wrong numero BS given [$numeroBs] got $amount results");
            $this->notFound[] = $facture;
            return;
        }

        return $netBSMembre[0];
    }

    private function getFamille(WNGFacture $facture) {

        /** @var WNGMembre[] $membres */
        $membres = array_filter($this->wngMembres, function(WNGMembre $membre) use ($facture) {
            return $membre->idFamille === $facture->idFamille;
        });

        if (count($membres) === 0) {
            // $this->io->writeln("un membre de la famille a pas été trouvé {$facture->idFamille}");
            $prenoms = array_filter(explode(' ', $facture->remarques), function($str) { return !empty($str); });
            $goodFamilles = [];
            foreach($this->familles as $famille) {
                $famillePrenoms = [];
                foreach($famille->getMembres() as $m) $famillePrenoms[] = $m->getPrenom();

                if (count(array_intersect($prenoms, $famillePrenoms)) === count($prenoms))
                    $goodFamilles[] = $famille;
            }

            if(count($goodFamilles) === 1)
                return $goodFamilles[0];

            $this->notFound[] = $facture;
            return null;
        }

        foreach($membres as $m) {
            $result = $this->getMembre($facture, $m->idMembre);
            if ($result) return $result->getFamille();
        }

        $this->io->writeln("On a la famille mais pas trouvé de membre lowl");
        return null;
    }

    private function toFacture(WNGFacture $WNGFacture) {

        $facture    = new Facture();
        $facture->_setOldFichierId($WNGFacture->idFacture);
        $facture->setStatut(Facture::OUVERTE);
        $facture->setDate($WNGFacture->dateFacture);
        $facture->setCompteToUse($this->compte);
        $facture->setRemarques($WNGFacture->remarques);
        if ($WNGFacture->dateImpressionFacture)
            $facture->setDateImpression($WNGFacture->dateImpressionFacture);

        switch($WNGFacture->statusFacture) {
            case 'payee':
                $this->setPayee($WNGFacture, $facture);
                if($WNGFacture->dateRappel1) $facture->addRappel($this->createRappel($WNGFacture, 1));
                if($WNGFacture->dateRappel2) $facture->addRappel($this->createRappel($WNGFacture, 2));
                break;

            case 'rappel_2':
                $facture->addRappel($this->createRappel($WNGFacture, 1));
                $facture->addRappel($this->createRappel($WNGFacture, 2));
                break;

            case 'rappel_1':
                $facture->addRappel($this->createRappel($WNGFacture, 1));
                break;
        }

        return $facture;
    }

    private function createRappel(WNGFacture $facture, $n) {
        $date = $n === 1 ? $facture->dateRappel1 : $facture->dateRappel2;
        $rappel = new Rappel();
        $rappel->setDate($date);
        $rappel->setDateImpression($date);
        return $rappel;
    }

    private function toCreance(WNGFacture $WNGFacture) {

        $creance    = new Creance();
        $creance->setTitre(WNGHelper::sanitize($WNGFacture->nomFacture));
        $creance->setRemarques(WNGHelper::sanitize($WNGFacture->remarques));
        $creance->setDate($WNGFacture->dateFacture);
        $creance->setRabais($WNGFacture->rabaisFacture);
        $creance->setMontant($WNGFacture->montantFacture);
        return $creance;
    }

    private function setPayee(WNGFacture $WNGFacture, Facture $facture) {

        $facture->setStatut(Facture::PAYEE);
        $paiement = new Paiement();
        $paiement->setMontant($WNGFacture->montantPayeFacture);
        $paiement->setDate($WNGFacture->datePayeFacture);
        $paiement->setCompte($this->compte);
        $facture->addPaiement($paiement);
    }
}
