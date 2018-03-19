<?php

namespace SauvabelinBundle\Import\Model;

use NetBS\FichierBundle\Entity\Famille;

class WNGFamille
{
    public $idFamille;

    public $nomFamille;

    public $adresseFamille;

    public $npaFamille;

    public $localiteFamille;

    /**
     * @var Famille
     */
    private $famille;

    public function __construct(array $d)
    {
        $this->idFamille        = $d['id_famille'];
        $this->nomFamille       = $d['nom_famille'];
        $this->adresseFamille   = $d['adresse_famille'];
        $this->npaFamille       = WNGHelper::toNumericString($d['npa_famille']);
        $this->localiteFamille  = $d['ville_famille'];

        $this->toFamille();
    }

    public function toFamille() {

        $famille    = new Famille();
        $famille->setNom($this->nomFamille)
            ->setValidity(Famille::VALIDE);

        $this->famille = $famille;
    }

    public function getFamille() {

        return $this->famille;
    }

}