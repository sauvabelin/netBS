<?php

namespace Ovesco\FacturationBundle\Model;

class ParsedBVR
{
    private $factures = [];

    private $orphanPaiements = [];

    /**
     * @return array
     */
    public function getFactures()
    {
        return $this->factures;
    }

    public function addFacture($facture) {
        $this->factures[] = $facture;
    }

    /**
     * @return array
     */
    public function getOrphanPaiements()
    {
        return $this->orphanPaiements;
    }

    public function addOrphanPaiement($orphanPaiement) {
        $this->orphanPaiements[] = $orphanPaiement;
    }
}