<?php

namespace SauvabelinBundle\Command;

use Ovesco\FacturationBundle\Entity\Compte;
use Ovesco\FacturationBundle\Entity\Creance;
use Ovesco\FacturationBundle\Entity\Facture;
use Ovesco\FacturationBundle\Entity\Paiement;
use Ovesco\FacturationBundle\Entity\Rappel;
use SauvabelinBundle\Import\Importator;
use SauvabelinBundle\Import\Model\WNGFacture;
use SauvabelinBundle\Import\Model\WNGHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportFacturesCommand extends ContainerAwareCommand
{
    protected $compte;

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
        $io = new SymfonyStyle($input, $output);

        $compte = new Compte();
        $compte->setCcp("10-1915-8");
        $compte->setIban("10-1915-8");
        $compte->setLine1("Brigade de Sauvabelin");
        $compte->setLine2("Lausanne");

        $manager->persist($compte);
        $this->compte = $compte;

        $importator = new Importator(
            $this->getContainer()->getParameter('database_host'),
            $this->getContainer()->getParameter('database_name'),
            $this->getContainer()->getParameter('database_user'),
            $this->getContainer()->getParameter('database_password')
        );

        $WNGMembres = $importator->loadMembres();
        $WNGFacturs = $importator->loadFactures();
        $notFound = 0;

        foreach($WNGFacturs as $WNGFacture) {

            $WNGMembre = isset($WNGMembres[$WNGFacture->idMembre]) ? $WNGMembres[$WNGFacture->idMembre] : null;
            if(!$WNGMembre) {
                $io->writeln("Couldnt find membre or famille for facture {$WNGFacture->nomFacture}");
                $notFound++;
                continue;
            }

            $numeroBs   = $WNGMembre->numeroMembre;

            if(empty($numeroBs)) {
                $io->writeln("Got empty numero BS for membre {$WNGMembre->prenom} {$WNGMembre->nom}");
                continue;
            }
            $netBSMembre = $manager->getRepository('SauvabelinBundle:BSMembre')->findBy(['numeroBS' => $numeroBs]);
            $amount = count($netBSMembre);

            if($amount !== 1) {
                $notFound++;
                $io->writeln("Wrong numero BS given [$numeroBs] got $amount results");
                continue;
            }

            $netBSMembre = $netBSMembre[0];
            $creance = $this->toCreance($WNGFacture);
            $creance->setDebiteur($netBSMembre);
            $manager->persist($creance);

            if($WNGFacture->statusFacture !== 'non_payee') {
                $facture = $this->toFacture($WNGFacture);
                $facture->setDebiteur($netBSMembre);
                $facture->addCreance($creance);
                $manager->persist($facture);
            }
        }

        $manager->flush();

        $io->warning("Got $notFound unmapped factures");
    }

    private function toFacture(WNGFacture $WNGFacture) {

        $facture    = new Facture();
        $facture->_setOldFichierId($WNGFacture->idFacture);
        $facture->setStatut(Facture::OUVERTE);
        $facture->setDate($WNGFacture->dateFacture);
        $facture->setCompteToUse($this->compte);

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
