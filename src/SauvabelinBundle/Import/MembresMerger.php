<?php

namespace SauvabelinBundle\Import;

use Doctrine\Common\Collections\ArrayCollection;
use SauvabelinBundle\Import\Model\WNGHelper;
use SauvabelinBundle\Import\Model\WNGMembre;

class MembresMerger
{
    /**
     * @var ArrayCollection[]
     */
    private $pool = [];

    /**
     * MembresMerger constructor.
     */
    public function __construct()
    {
    }

    public function generatePool(array $membres) {

        foreach($membres as $m1)
            foreach ($membres as $m2)
                $this->isOfSameFamily($m1, $m2);

        return $this->pool;
    }

    public function isOfSameFamily(WNGMembre $membre1, WNGMembre $membre2) {

        if($membre1 === $membre2)
            return;

        if(WNGHelper::similar($membre1->nom, $membre2->nom) > 90)
            if($this->similarAddress($membre1, $membre2))
                $this->merge($membre1, $membre2);
    }

    private function merge(WNGMembre $membre1, WNGMembre $membre2) {

        foreach($this->pool as $collection) {

            if($collection->contains($membre1) && !$collection->contains($membre2)) {
                $collection->add($membre2);
                return;
            }

            elseif($collection->contains($membre2) && !$collection->contains($membre1)) {
                $collection->add($membre1);
                return;
            }

            elseif($collection->contains($membre1) && $collection->contains($membre2))
                return;
        }

        $this->pool[]   = new ArrayCollection([$membre1, $membre2]);
    }

    private function similarAddress(WNGMembre $m1, WNGMembre $m2) {

        $rue1   = $this->trimAdresse($m1->adresse);
        $rue2   = $this->trimAdresse($m2->adresse);

        if(WNGHelper::similar($rue1, $rue2) > 85)
            if(WNGHelper::toNumericString($rue1) === WNGHelper::toNumericString($rue2))
                if($m1->npa === $m2->npa)
                    return true;

        return false;
    }

    private function trimAdresse($rue) {

        $rue        = strtolower($rue);

        if(strpos($rue, "ch. ") === 0)
            $rue    = substr($rue, strlen("ch. "));
        elseif(strpos($rue, "ch ") === 0)
            $rue    = substr($rue, strlen("ch "));
        elseif(strpos($rue, "chemin du ") === 0)
            $rue    = substr($rue, strlen("chemin du "));
        elseif(strpos($rue, "chemin de ") === 0)
            $rue    = substr($rue, strlen("chemin de "));

        return $rue;
    }
}
