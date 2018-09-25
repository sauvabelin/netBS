<?php

namespace SauvabelinBundle\Model;

use NetBS\FichierBundle\Model\Search\SearchBaseMembreInformation;

class SearchMembre extends SearchBaseMembreInformation
{
    /**
     * @var boolean
     */
    private $noAdabs;

    /**
     * @return bool
     */
    public function isNoAdabs()
    {
        return $this->noAdabs;
    }

    /**
     * @param bool $noAdabs
     */
    public function setNoAdabs($noAdabs)
    {
        $this->noAdabs = $noAdabs;
    }
}