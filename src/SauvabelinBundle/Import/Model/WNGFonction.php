<?php

namespace SauvabelinBundle\Import\Model;

class WNGFonction
{
    public $idFonction;

    public $nom;

    public $abbreviation;

    public function __construct(array $d)
    {
        $this->idFonction   = $d['id_attribution'];
        $this->nom          = $d['nom_attribution'];
        $this->abbreviation = $d['abrev_attribution'];
    }
}