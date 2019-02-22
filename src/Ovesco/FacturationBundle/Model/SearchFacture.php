<?php

namespace Ovesco\FacturationBundle\Model;

use Ovesco\FacturationBundle\Entity\Facture;

class SearchFacture
{
    public $factureId;

    public $montant;

    public $montantPaye;

    public $compteToUse;

    public $nombreDeRappels;

    public $nombreDeCreances;

    public $titreCreance;

    public $date;

    public $dateImpression;

    public $isPrinted;

    public $datePaiement;

    public $statut = Facture::OUVERTE;
}
