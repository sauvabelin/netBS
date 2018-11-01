<?php

namespace SauvabelinBundle\Import\Model;

class WNGFacture
{
    public $idFacture;

    public $idMembre;

    public $idFamille;

    public $nomFacture;

    public $montantFacture;

    public $montantPayeFacture;

    public $rabaisFacture;

    public $dateFacture;

    public $dateImpressionFacture;

    public $datePayeFacture;

    public $dateRappel1;

    public $dateRappel2;

    public $statusFacture;

    public $remarques;

    public function __construct(array $data)
    {
        $this->idFacture    = intval($data['id_facture']);
        $this->idMembre     = intval($data['id_membre']);
        $this->idFamille    = intval($data['id_famille']);
        $this->nomFacture   = $data['nom_facture'];
        $this->montantFacture   = $data['montant_facture'];
        $this->montantPayeFacture = $data['montant_paye_facture'];
        $this->rabaisFacture    = $data['rabais_facture'];
        $this->dateFacture  = WNGHelper::toDatetime($data['date_facture']);
        $this->dateImpressionFacture = WNGHelper::toDatetime($data['date_impression_facture']);
        $this->datePayeFacture = WNGHelper::toDatetime($data['date_paye_facture']);
        $this->dateRappel1 = WNGHelper::toDatetime($data['date_rappel_1']);
        $this->dateRappel2 = WNGHelper::toDatetime($data['date_rappel_2']);
        $this->statusFacture = $data['status_facture'];
        $this->remarques = $data['remarques_facture'];
    }
}