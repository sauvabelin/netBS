<?php

namespace Ovesco\FacturationBundle\Model;

use Ovesco\FacturationBundle\Entity\Facture;

class SearchFacture extends Facture
{
    public $montant;

    public $montantPaye;

    public $nombreDeRappels;

    public $nombreDeCreances;

    public $titreCreance;

    public $date;

    public $dateImpression;

    public $statut;
}
