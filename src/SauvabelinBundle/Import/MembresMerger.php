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

        foreach($membres as $m1) {

            $foundFamily = false;

            foreach ($membres as $m2) {

                if($m1 === $m2)
                    continue;

                if(WNGHelper::similar($m1->nom, $m2->nom) > 90 && self::similarAddress($m1, $m2)) {

                    $foundFamily = true;
                    $this->merge($m1, $m2);
                    break;
                }
            }

            if(!$foundFamily)
                $this->pool[] = new ArrayCollection([$m1]);
        }

        return $this->pool;
    }

    private function merge(WNGMembre $membre1, WNGMembre $membre2) {

        foreach($this->pool as $collection) {

            if($collection->contains($membre1) && !$collection->contains($membre2)) {
                $collection->add($membre2);
                return;
            }

            if($collection->contains($membre2) && !$collection->contains($membre1)) {
                $collection->add($membre1);
                return;
            }

            elseif($collection->contains($membre1) && $collection->contains($membre2))
                return;
        }

        $this->pool[]   = new ArrayCollection([$membre1, $membre2]);
    }

    public static function similarAddress(WNGMembre $m1 = null, WNGMembre $m2 = null) {

        if($m1 === null && $m2 === null)
            return true;

        if($m1 === null || $m2 === null)
            return false;

        $rue1   = self::trimAdresse($m1->adresse);
        $rue2   = self::trimAdresse($m2->adresse);

        if(WNGHelper::similar($rue1, $rue2) > 85)
            if($m1->npa === $m2->npa)
                return true;

        return false;
    }

    public static function trimAdresse($rue) {

        $rue        = strtolower($rue);

        if(strpos($rue, "chemin du ") === 0)
            $rue    = substr($rue, strlen("chemin du "));
        elseif(strpos($rue, "chemin des ") === 0)
            $rue    = substr($rue, strlen("chemin des "));
        elseif(strpos($rue, "chemin de ") === 0)
            $rue    = substr($rue, strlen("chemin de "));
        elseif(strpos($rue, "chemin ") === 0)
            $rue    = substr($rue, strlen("chemin "));

        elseif(strpos($rue, "ch. du ") === 0)
            $rue    = substr($rue, strlen("ch. du "));
        elseif(strpos($rue, "ch du ") === 0)
            $rue    = substr($rue, strlen("ch du "));
        elseif(strpos($rue, "ch. des ") === 0)
            $rue    = substr($rue, strlen("ch. des "));
        elseif(strpos($rue, "ch des ") === 0)
            $rue    = substr($rue, strlen("ch des "));
        elseif(strpos($rue, "ch. ") === 0)
            $rue    = substr($rue, strlen("ch. "));
        elseif(strpos($rue, "ch ") === 0)
            $rue    = substr($rue, strlen("ch "));
        elseif(strpos($rue, "ch. de ") === 0)
            $rue    = substr($rue, strlen("ch. de "));
        elseif(strpos($rue, "ch de ") === 0)
            $rue    = substr($rue, strlen("ch de "));

        return trim($rue);
    }
}
