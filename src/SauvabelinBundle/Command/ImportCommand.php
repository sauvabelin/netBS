<?php

namespace SauvabelinBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use NetBS\FichierBundle\Entity\Geniteur;
use NetBS\FichierBundle\Entity\Membre;
use NetBS\FichierBundle\Entity\ObtentionDistinction;
use NetBS\FichierBundle\Mapping\BaseFamille;
use NetBS\FichierBundle\Utils\Entity\ValidityTrait;
use SauvabelinBundle\Entity\BSMembre;
use SauvabelinBundle\Import\AttributionsAssigner;
use SauvabelinBundle\Import\Importator;
use SauvabelinBundle\Import\MembresMerger;
use SauvabelinBundle\Import\Model\WNGHelper;
use SauvabelinBundle\Import\Model\WNGMembre;
use SauvabelinBundle\Import\Model\WNGObtentionDistinction;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends ContainerAwareCommand
{
    private $io;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sauvabelin:import:wng');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '2048M');

        $this->manager      = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $this->io           = new SymfonyStyle($input, $output);
        $merger             = new MembresMerger();


        $importator         = new Importator(
            $this->getContainer()->getParameter('database_host'),
            $this->getContainer()->getParameter('database_name'),
            $this->getContainer()->getParameter('database_user'),
            $this->getContainer()->getParameter('database_password')
        );

        $this->io->writeln("Loading remote data in memory");
        $WNGMembres         = $importator->loadMembres();
        $WNGAttributions    = $importator->loadAttributions();
        $WNGDistinctions    = $importator->loadDistinctions();
        $WNGFonctions       = $importator->loadFonctions();
        $WNGODs             = $importator->loadObtentionsDistinctions();
        $WNGUnites          = $importator->loadGroupes();

        $this->io->table(
            ["membres", "attributions", "obtentions distinction"],
            [[count($WNGMembres), count($WNGAttributions), count($WNGODs)]]
        );

        $this->io->writeln("Map attributions");

        foreach($WNGAttributions as $attribution) {

            if(isset($WNGUnites[$attribution->idUnite]))
                $attribution->WNGUnite      = $WNGUnites[$attribution->idUnite];

            if(isset($WNGFonctions[$attribution->idFonction]))
                $attribution->WNGFonction   = $WNGFonctions[$attribution->idFonction];

            if(isset($WNGMembres[$attribution->idMembre]))
                $WNGMembres[$attribution->idMembre]->WNGAttributions[] = $attribution;
        }


        $this->io->writeln("Map distinctions");

        foreach($WNGODs as $obtentionDistinction) {

            if(isset($WNGDistinctions[$obtentionDistinction->idDistinction]))
                $obtentionDistinction->WNGDistinction   = $WNGDistinctions[$obtentionDistinction->idDistinction];

            if(isset($WNGMembres[$obtentionDistinction->idMembre]))
                $WNGMembres[$obtentionDistinction->idMembre]->WNGObtentionsDistinctions[] = $obtentionDistinction;
        }

        $this->io->writeln("Merge members");
        $pool   = $merger->generatePool($WNGMembres);

        $this->io->writeln("Handling members pools");
        $progress   = $this->io->createProgressBar(count($pool));
        $membersCount   = 0;

        foreach($pool as $collection) {

            $membersCount += count($collection);
            $this->handlePool($collection);
            $progress->advance();
        }

        $progress->finish();
        $this->io->success("Handled $membersCount members");
        $this->io->writeln("Flushing data");

        $this->manager->flush();

    }

    private function handlePool(ArrayCollection $collection) {

        if(count($collection) === 0)
            return;

        $config         = $this->getContainer()->get('netbs.fichier.config');
        $manager        = $this->manager;
        $bestAdresse    = $this->extractBest($collection, function(WNGMembre $m1, WNGMembre $m2) {
            return MembresMerger::similarAddress($m1, $m2);
        });

        $bestTelephone  = $this->extractBest($collection, function(WNGMembre $m1, WNGMembre $m2) {
            return WNGHelper::toNumericString($m1->telephone) === WNGHelper::toNumericString($m2->telephone);
        });

        $bestEmail      = $this->extractBest($collection, function(WNGMembre $m1, WNGMembre $m2) {
            return WNGHelper::toEmail($m1->email) === WNGHelper::toEmail($m2->email);
        });

        $famille        = $config->createFamille();
        $famille->setValidity(BaseFamille::VALIDE);
        $famille->setNom(WNGHelper::sanitize($collection[0]->nom));

        if($bestAdresse && $bestAdresse->getNetBSAdresse())
            $famille->addAdresse($bestAdresse->getNetBSAdresse());
        if($bestTelephone && $bestTelephone->getNetBSTelephone())
            $famille->addTelephone($bestTelephone->getNetBSTelephone());
        if($bestEmail && $bestEmail->getNetBSEmail())
            $famille->addEmail($bestEmail->getNetBSEmail());

        foreach($collection as $WNGMembre) {

            $membre = $this->convertToNetBSMembre($WNGMembre, $bestAdresse, $bestTelephone, $bestEmail);
            $manager->persist($membre);
            $famille->addMembre($membre);
        }

        foreach($this->extractGeniteurs($collection) as $geniteur) {

            $manager->persist($geniteur);
            $famille->addGeniteur($geniteur);
        }

        $manager->persist($famille);
    }

    /**
     * @param ArrayCollection<WNGMembre> $collection
     * @return array|Geniteur[]
     */
    private function extractGeniteurs(ArrayCollection $collection) {

        $config     = $this->getContainer()->get('netbs.fichier.config');

        /** @var Geniteur[] $geniteurs */
        $geniteurs  = [];

        /** @var WNGMembre $WNGMembre */
        foreach($collection as $WNGMembre) {

            $mere       = $WNGMembre->getNetBSMere($config);
            $pere       = $WNGMembre->getNetBSPere($config);
            $foundPere  = false;
            $foundMere  = false;

            foreach($geniteurs as $NetBSGeniteur) {

                if($mere && $NetBSGeniteur->getPrenom() === $mere->getPrenom())
                    $foundMere = true;
                if($pere && $NetBSGeniteur->getPrenom() === $pere->getPrenom())
                    $foundPere = true;
            }

            if($mere && !$foundMere)
                $geniteurs[] = $mere;
            if($pere && !$foundPere)
                $geniteurs[] = $pere;
        }

        return $geniteurs;
    }

    /**
     * @param ArrayCollection $collection
     * @param $fn
     * @return WNGMembre|null
     */
    private function extractBest(ArrayCollection $collection, $fn) {

        if(count($collection) === 1)
            return $collection[0];

        $items  = [];
        $score  = [];

        for($i = 0; $i < count($collection); $i++) {

            $found = false;

            for($j = 0; $j < count($items); $j++) {
                if($fn($items[$j], $collection[$i])) {
                    $score[$j]++;
                    $found = true;
                }
            }

            if(!$found) {

                $items[] = $collection[$i];
                $score[] = 1;
            }
        }

        $best = 0;
        for($i = 0; $i < count($score); $i++)
            if($score[$i] > $score[$best])
                $best = $i;

        if($score[$best] > 1)
            return $items[$best];

        return null;
    }

    private function convertToNetBSMembre(WNGMembre $membre, WNGMembre $bestAdresse = null, WNGMembre $bestTelephone = null, WNGMembre $bestEmail = null) {

        $baseDate       = \DateTime::createFromFormat("Y", "1000");
        $config         = $this->getContainer()->get('netbs.fichier.config');
        $manager        = $this->manager;
        $assigner       = new AttributionsAssigner($manager);
        $inscription    = $membre->inscription ? $membre->inscription : $baseDate;
        $naissance      = $membre->dateNaissance ? $membre->dateNaissance : $baseDate;

        /** @var BSMembre $netBSMembre */
        $netBSMembre    = $config->createMembre();
        $attributions   = $assigner->generate($membre);
        $netBSMembre->setPrenom(WNGHelper::sanitize($membre->prenom));
        $netBSMembre->setNumeroBS($membre->numeroMembre);
        $netBSMembre->setInscription($inscription);
        $netBSMembre->setNaissance($naissance);
        $netBSMembre->setSexe($membre->sexe);
        $netBSMembre->setDesinscription($membre->demission);

        if($membre->getNetBSNatel())
            $netBSMembre->addTelephone($membre->getNetBSNatel());

        $statut = null;

        switch (intval($membre->idFichier)) {
            case 2:
                $statut = Membre::INSCRIT;
                break;
            case 4:
                $statut = Membre::DESINSCRIT;
                break;
            case 5:
                $statut = Membre::DECEDE;
                break;
            default:
                $statut = Membre::INSCRIT;
                break;
        }

        $netBSMembre->setStatut($statut);


        if($membre->getNetBSAdresse()) {
            if (!$bestAdresse ||  !MembresMerger::similarAddress($membre, $bestAdresse)) {
                $netBSMembre->addAdresse($membre->getNetBSAdresse());
            }
        }

        if($membre->getNetBSTelephone())
            if(!$bestTelephone || (WNGHelper::toNumericString($membre->telephone)
                && WNGHelper::toNumericString($membre->telephone) !== WNGHelper::toNumericString($bestTelephone->telephone)))
                $netBSMembre->addTelephone($membre->getNetBSTelephone());

        if($membre->getNetBSEmail())
            if(!$bestEmail || (WNGHelper::toEmail($membre->email)
                && WNGHelper::toEmail($membre->email) !== WNGHelper::toEmail($bestEmail->email)))
                $netBSMembre->addEmail($membre->getNetBSEmail());

        //Assignation attributions et distinctions
        foreach($attributions as $attribution) {

            $manager->persist($attribution);
            $netBSMembre->addAttribution($attribution);
        }

        foreach($this->assignDistinctions($membre) as $od) {

            $manager->persist($od);
            $netBSMembre->addObtentionDistinction($od);
        }

        return $netBSMembre;
    }

    private function assignDistinctions(WNGMembre $membre) {

        $ods    = [];

        /** @var WNGObtentionDistinction $WNGObtentionsDistinction */
        foreach($membre->WNGObtentionsDistinctions as $WNGObtentionsDistinction) {

            $distinction    = $this->searchDistinction($WNGObtentionsDistinction->WNGDistinction->nom);

            if(!$distinction || !$WNGObtentionsDistinction->date)
                continue;

            $od = new ObtentionDistinction();
            $od->setDistinction($distinction);
            $od->setDate($WNGObtentionsDistinction->date);
            $ods[] = $od;
        }

        return $ods;
    }

    private function searchDistinction($distinction) {

        return $this->getContainer()->get('doctrine.orm.default_entity_manager')
            ->getRepository('NetBSFichierBundle:Distinction')->findOneBy(array('nom' => $distinction));
    }
}
