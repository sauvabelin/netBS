<?php

namespace SauvabelinBundle\Import;

use Doctrine\ORM\EntityManager;
use NetBS\FichierBundle\Entity\Attribution;
use NetBS\FichierBundle\Mapping\BaseGroupe;
use SauvabelinBundle\Import\Model\WNGAttribution;
use SauvabelinBundle\Import\Model\WNGFonction;
use SauvabelinBundle\Import\Model\WNGHelper;
use SauvabelinBundle\Import\Model\WNGMembre;
use SauvabelinBundle\Import\Model\WNGUnite;

class AttributionsAssigner
{
    private $manager;

    private $fonctions;

    private $groupes;

    private $migrationsGroupe;

    private $migrationFonction;

    private $APMBSGroupe;

    private $ADABSGroupe;

    private $associationFonction;

    public function __construct(EntityManager $manager)
    {
        $this->manager      = $manager;
        $groupeRepo         = $manager->getRepository('SauvabelinBundle:BSGroupe');
        $fonctionRepo       = $manager->getRepository('NetBSFichierBundle:Fonction');
        $this->fonctions    = $fonctionRepo->findAll();
        $this->groupes      = $groupeRepo->findAll();

        $this->migrationsGroupe     = $groupeRepo->findOneBy(array('nom' => 'migration'));
        $this->APMBSGroupe          = $groupeRepo->findOneBy(array('nom' => 'APMBS'));
        $this->ADABSGroupe          = $groupeRepo->findOneBy(array('nom' => 'ADABS'));
        $this->migrationFonction    = $fonctionRepo->findOneBy(array('abbreviation'    => 'INCONNU'));
        $this->associationFonction  = $fonctionRepo->findOneBy(array('abbreviation'    => 'membre'));
    }

    public function generate(WNGMembre $WNGMembre) {

        $attributions   = [];

        if($this->adabsAttribution($WNGMembre))
            $attributions[] = $this->adabsAttribution($WNGMembre);

        foreach($WNGMembre->WNGAttributions as $WNGAttribution) {

            if($WNGAttribution->WNGFonction === null || $WNGAttribution->WNGUnite === null)
                continue;

            if($WNGAttribution->dateDebut === null)
                continue;

            if(WNGHelper::similar("ATTRIBUTION FACTICE", $WNGAttribution->remarques) > 75)
                continue;

            if(WNGHelper::similar($WNGAttribution->WNGFonction->nom, "Rien") > 75)
                continue;

            if(WNGHelper::similar($WNGAttribution->WNGUnite->nomUnite, "Autre") > 90)
                continue;

            $attribution    = new Attribution();
            $groupData      = $this->findGroupe($WNGAttribution);

            $attribution->setGroupe($groupData['groupe']);
            $attribution->setRemarques(WNGHelper::sanitize($groupData['remarques']));
            $attribution->setFonction($this->findFonction($WNGAttribution->WNGFonction));
            $attribution->setDateDebut($WNGAttribution->dateDebut);

            if($WNGAttribution->dateFin)
                $attribution->setDateFin($WNGAttribution->dateFin);

            $attributions[] = $attribution;
        }

        return $attributions;
    }

    private function adabsAttribution(WNGMembre $WNGMembre) {

        if($WNGMembre->idFichier === '2' || $WNGMembre->inscriptionAdabs instanceof \DateTime) {

            $attribution    = new Attribution();
            $attribution->setGroupe($this->ADABSGroupe);
            $attribution->setFonction($this->associationFonction);
            $attribution->setDateDebut($WNGMembre->inscriptionAdabs);

            if($WNGMembre->demissionAdabs)
                $attribution->setDateFin($WNGMembre->demissionAdabs);

            return $attribution;
        }

        return false;
    }

    private function findFonction(WNGFonction $fonction) {

        foreach($this->fonctions as $BSFonction)
            if(strtolower($BSFonction->getAbbreviation()) === strtolower($fonction->abbreviation)
            || strtolower($BSFonction->getNom()) === strtolower($fonction->nom))
                return $BSFonction;

        return $this->migrationFonction;
    }

    private function findGroupe(WNGAttribution $WNGAttribution) {

        $WNGUnite               = $WNGAttribution->WNGUnite;
        $chosenGroupe           = null;
        $remarques              = $WNGAttribution->remarques;

        foreach($this->groupes as $netBSGroupe) {

            if(WNGHelper::similar($netBSGroupe->getNom(), $this->cleanGroupeName($WNGUnite->nomUnite)) > 85
                || WNGHelper::similar($netBSGroupe->getGroupeType()->getNom() . " " . $netBSGroupe->getNom(), $this->cleanGroupeName($WNGUnite->nomUnite)) > 85) {
                $chosenGroupe = $netBSGroupe;

                foreach ($netBSGroupe->getEnfants() as $child) {
                    if (WNGHelper::similar($child->getNom(), $this->cleanGroupeName($WNGAttribution->remarques)) > 77
                        || WNGHelper::similar($child->getGroupeType()->getNom() . " " . $child->getNom(), $this->cleanGroupeName($WNGAttribution->remarques)) > 77) {
                        $chosenGroupe = $child;
                        break 2;
                    }
                }

                break;
            }
        }

        if($chosenGroupe === null) {

            $chosenGroupe  = $this->migrationsGroupe;
            $remarques = "Données précédentes: Unité: {$WNGUnite->nomUnite}, Remarques: {$WNGAttribution->remarques}";
        }

        return [
            'groupe'    => $chosenGroupe,
            'remarques' => $remarques
        ];
    }

    private function cleanGroupeName($str) {

        if($str === "Le Clan")
            return $str;

        $str = strtolower($str);
        $str = WNGHelper::sanitize($str);
        $str = str_replace("troupe de ", "", $str);
        $str = str_replace("meute de ", "", $str);
        $str = str_replace("clan ", "", $str);

        if($str === "santis")
            return "säntis";

        return $str;
    }
}