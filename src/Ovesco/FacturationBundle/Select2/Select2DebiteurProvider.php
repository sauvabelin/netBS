<?php

namespace Ovesco\FacturationBundle\Select2;

use Doctrine\Common\Collections\Collection;
use NetBS\CoreBundle\Select2\Select2ProviderInterface;
use NetBS\FichierBundle\Mapping\BaseFamille;
use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\FichierBundle\Select2\FamilleProvider;
use NetBS\FichierBundle\Select2\MembreProvider;

class Select2DebiteurProvider implements Select2ProviderInterface
{
    private $membreProvider;

    private $familleProvider;

    public function __construct(MembreProvider $membreProvider, FamilleProvider $familleProvider)
    {
        $this->membreProvider   = $membreProvider;
        $this->familleProvider  = $familleProvider;
    }

    /**
     * Returns the class of the items managed by this provider
     * @return string
     */
    public function getManagedClass()
    {
        return "facturation_debiteur";
    }

    /**
     * Returns string representation of the given managed object
     * @param BaseMembre|BaseFamille $item
     * @return string
     */
    public function toString($item)
    {
        return $item->__toString();
    }

    /**
     * Returns the unique id for the item, used in the select2 field
     * @param BaseMembre|BaseFamille $item
     * @return string
     */
    public function toId($item)
    {
        return ($item instanceof BaseMembre ? 'membre:' : 'famille:') . $item->getId();
    }

    /**
     * Search for objects related to the given needle
     * @param $needle
     * @param int $limit
     * @return Collection
     */
    public function search($needle, $limit = 5)
    {
        $membres = $this->membreProvider->search($needle, $limit);
        $familles = $this->familleProvider->search($needle, $limit);
        $membres = is_array($membres) ? $membres : $membres->toArray();
        $familles = is_array($familles) ? $familles : $familles->toArray();

        $result = array_merge($membres, $familles);
    }
}