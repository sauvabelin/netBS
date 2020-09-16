<?php

namespace TDGLBundle\Model;

use NetBS\FichierBundle\Model\Search\SearchBaseMembreInformation;

class TDGLMembreSearch extends SearchBaseMembreInformation
{
    protected $totem;

    /**
     * @return mixed
     */
    public function getTotem()
    {
        return $this->totem;
    }

    /**
     * @param mixed $totem
     */
    public function setTotem($totem)
    {
        $this->totem = $totem;
    }
}
