<?php

namespace SauvabelinBundle\Import\Model;

use NetBS\FichierBundle\Entity\Adresse;
use NetBS\FichierBundle\Entity\Email;
use NetBS\FichierBundle\Entity\Geniteur;
use NetBS\FichierBundle\Entity\Telephone;
use NetBS\FichierBundle\Mapping\Personne;
use NetBS\FichierBundle\Service\FichierConfig;

class WNGMembre
{
    public $idMembre;

    public $idFamille;

    public $idFichier;

    public $numeroMembre;

    public $nom;

    public $prenom;

    public $sexe;

    public $nomPere;

    public $prenomPere;

    public $professionPere;

    public $nomMere;

    public $prenomMere;

    public $professionMere;

    public $adresse;

    public $npa;

    public $localite;

    public $numeroAvs;

    public $dateNaissance;

    public $telephone;

    public $natel;

    public $email;

    public $msn;

    public $inscription;

    public $inscriptionAdabs;

    public $demission;

    public $demissionAdabs;

    public $remarques;

    /** @var WNGAttribution[] */
    public $WNGAttributions = [];

    /** @var array $WNGObtentionsDistinctions[] */
    public $WNGObtentionsDistinctions = [];

    public function __construct(array $d)
    {
        $this->idMembre             = $d['id_membre'];
        $this->idFamille            = $d['id_famille'];
        $this->idFichier            = intval($d['id_fichier']);
        $this->numeroMembre         = intval($d['no_membre']);
        $this->nom                  = $d['nom'];
        $this->prenom               = $d['prenom'];
        $this->sexe                 = $d['sexe'] == 'm' ? Personne::HOMME : Personne::FEMME;
        $this->nomPere              = WNGHelper::isEmpty($d['nom_pere']) ? null : $d['nom_pere'];
        $this->prenomPere           = WNGHelper::isEmpty($d['prenom_pere']) ? null : $d['prenom_pere'];
        $this->professionPere       = WNGHelper::isEmpty($d['profession_pere']) ? null : $d['profession_pere'];
        $this->nomMere              = WNGHelper::isEmpty($d['nom_mere']) ? null : $d['nom_mere'];
        $this->prenomMere           = WNGHelper::isEmpty($d['prenom_mere']) ? null : $d['prenom_mere'];
        $this->professionMere       = WNGHelper::isEmpty($d['profession_mere']) ? null : $d['profession_mere'];
        $this->adresse              = WNGHelper::isEmpty($d['adresse']) ? null : $d['adresse'];
        $this->npa                  = intval(WNGHelper::toNumericString($d['npa']));
        $this->localite             = $d['ville'];
        $this->numeroAvs            = $d['no_avs'];
        $this->dateNaissance        = WNGHelper::toDatetime($d['date_naissance']);
        $this->telephone            = WNGHelper::toNumericString($d['tel']);
        $this->natel                = WNGHelper::toNumericString($d['natel']);
        $this->email                = WNGHelper::toEmail($d['email']);
        $this->msn                  = WNGHelper::toEmail($d['adresse_msn']);
        $this->inscription          = WNGHelper::toDatetime($d['date_inscription_bs']);
        $this->inscriptionAdabs     = WNGHelper::toDatetime($d['date_inscription_adabs']);
        $this->demission            = WNGHelper::toDatetime($d['date_demission_bs']);
        $this->demissionAdabs       = WNGHelper::toDatetime($d['date_demission_adabs']);
        $this->remarques            = $d['remarques_membre'];
    }

    public function getNetBSAdresse() {

        if($this->adresse === null)
            return null;

        $adresse    = new Adresse();
        $adresse->setRue(WNGHelper::sanitize($this->adresse));
        $adresse->setNpa($this->npa);
        $adresse->setLocalite(WNGHelper::sanitize($this->localite));

        return $adresse;
    }

    public function getNetBSTelephone() {

        if($this->telephone === null)
            return null;

        $number = $this->telephone;
        if($number[0] !== 0 && strlen($number) === 9)
            $number = "0" . $number;

        return new Telephone($number);
    }

    public function getNetBSEmail() {

        if($this->email === null)
            return null;

        return new Email(WNGHelper::toEmail($this->email));
    }

    /**
     * @return Geniteur|null
     */
    public function getNetBSMere(FichierConfig $config) {

        if(!$this->prenomMere)
            return null;

        $mere   = $config->createGeniteur();
        if($this->nomMere && !WNGHelper::similar($this->nom, $this->nomMere) > 90)
            $mere->setNom($this->nomMere);

        $mere->setProfession(WNGHelper::sanitize($this->professionMere));
        $mere->setPrenom(WNGHelper::sanitize($this->prenomMere));
        $mere->setStatut(Geniteur::MERE);
        $mere->setSexe(Personne::FEMME);
        return $mere;
    }

    /**
     * @return Geniteur|null
     */
    public function getNetBSPere(FichierConfig $config) {

        if(!$this->prenomPere)
            return null;

        $pere   = $config->createGeniteur();
        if($this->nomPere && !WNGHelper::similar($this->nom, $this->nomPere) > 90)
            $pere->setNom($this->nomPere);

        $pere->setProfession(WNGHelper::sanitize($this->professionPere));
        $pere->setPrenom(WNGHelper::sanitize($this->prenomPere));
        $pere->setStatut(Geniteur::PERE);
        $pere->setSexe(Personne::HOMME);
        return $pere;
    }
}