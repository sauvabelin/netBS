<?php

namespace SauvabelinBundle\Import\Model;

use NetBS\FichierBundle\Entity\Email;
use NetBS\FichierBundle\Entity\Telephone;
use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\FichierBundle\Mapping\Personne;
use SauvabelinBundle\Entity\BSMembre;

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

    private $membre;

    public function __construct(array $d)
    {
        $this->idMembre             = $d['id_membre'];
        $this->idFamille            = $d['id_famille'];
        $this->idFichier            = intval($d['id_fichier']);
        $this->numeroMembre         = $d['no_membre'];
        $this->nom                  = $d['nom'];
        $this->prenom               = $d['prenom'];
        $this->sexe                 = $d['sexe'] == 'm' ? Personne::HOMME : Personne::FEMME;
        $this->nomPere              = $d['nom_pere'];
        $this->prenomPere           = $d['prenom_pere'];
        $this->professionPere       = $d['profession_pere'];
        $this->nomMere              = $d['nom_mere'];
        $this->prenomMere           = $d['prenom_mere'];
        $this->professionMere       = $d['profession_mere'];
        $this->rue                  = $d['adresse'];
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

        $this->membre   = $this->toMembre();
    }

    protected function toMembre() {

        $membre = new BSMembre();

        if($this->inscription)
            $membre->setInscription($this->inscription);

        if($this->dateNaissance)
            $membre->setNaissance($this->dateNaissance);

        $membre
            ->setNumeroBS($this->numeroMembre)
            ->setDesinscription($this->demission)
            ->setPrenom($this->prenom)
            ->setSexe($this->sexe);

        $statut = null;

        switch (intval($this->idFichier)) {
            case 4:
                $statut = BaseMembre::DESINSCRIT;
                break;
            case 5:
                $statut = BaseMembre::DECEDE;
                break;
            default:
                $statut = BaseMembre::INSCRIT;
                break;
        }

        $membre->setStatut($statut);

        $email  = WNGHelper::toEmail($this->email);
        $msn    = WNGHelper::toEmail($this->msn);
        $phone  = WNGHelper::toNumericString($this->telephone);
        $natel  = WNGHelper::toNumericString($this->natel);

        if($email)
            $membre->addEmail(new Email($email));
        if($msn)
            $membre->addEmail(new Email($msn));
        if($phone)
            $membre->addTelephone(new Telephone($phone));
        if($natel)
            $membre->addTelephone(new Telephone($natel));

        return $membre;
    }
}