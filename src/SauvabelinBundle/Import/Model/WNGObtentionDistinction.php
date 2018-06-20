<?php

namespace SauvabelinBundle\Import\Model;

class WNGObtentionDistinction
{
    public $idObtention;

    public $idDistinction;

    public $idMembre;

    public $date;

    public $WNGDistinction;

    public function __construct(array $d)
    {
        $this->idObtention      = $d['id_membres_distinction'];
        $this->idDistinction    = $d['id_distinction'];
        $this->idMembre         = $d['id_membre'];
        $this->date             = WNGHelper::toDatetime($d['date_membres_distinction']);
    }
}