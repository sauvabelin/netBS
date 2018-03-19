<?php

namespace SauvabelinBundle\Import\Model;

class WNGUnite
{
    public $idUnite;

    public $numeroUnite;

    public $nomUnite;

    public function __construct(array $d)
    {
        $this->idUnite      = $d['id_unite'];
        $this->numeroUnite  = $d['no_unite'];
        $this->nomUnite     = $d['nom_unite'];
    }
}