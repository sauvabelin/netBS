<?php

namespace SauvabelinBundle\Import\Model;

class WNGAttribution
{
    public $idAttribution;

    public $idMembre;

    public $idFonction;

    public $idUnite;

    public $dateDebut;

    public $dateFin;

    public $remarques;

    public $netBSGroupe;

    public $netBSFonction;

    public function __construct(array $d)
    {
        $this->idAttribution    = $d['id_membres_attribution'];
        $this->idMembre         = $d['id_membre'];
        $this->idFonction       = $d['id_attribution'];
        $this->idUnite          = $d['id_unite'];
        $this->dateDebut        = WNGHelper::toDatetime($d['date_debut_membres_attribution']);
        $this->dateFin          = WNGHelper::toDatetime($d['date_fin_membres_attribution']);
        $this->remarques        = $d['remarques_membres_attribution'];
    }
}