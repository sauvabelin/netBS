<?php

namespace SauvabelinBundle\Import\Model;

use NetBS\FichierBundle\Entity\Distinction;

class WNGDistinction
{
    public $idDistinction;

    public $nom;

    public $remarques;

    public function __construct(array $d)
    {
        $this->idDistinction    = $d['id_distinction'];
        $this->nom              = $d['nom_distinction'];
        $this->remarques        = $d['remarques_distinction'];

        $distinction            = new Distinction($this->nom);
        $distinction->setRemarques($this->remarques);
    }
}