<?php

namespace NetBS\FichierBundle\Exporter\Config;

use NetBS\CoreBundle\Exporter\Config\FPDFConfig;

class EtiquettesConfig
{
    public $FPDFConfig;

    public $margeInferieure             = 10;

    public $margeDroite                 = 10.9;

    public $colonnes                    = 4;

    public $lignes                      = 8;

    public $taillePolice                = 8;

    public $margeInterieureVerticale    = 10;

    public $margeInterieureHorizontale  = 10;

    public $displayNotAdressable        = true;

    public $title                       = "Aux parents de";

    public $mergeFamilles               = true;

    public function __construct()
    {
        $this->FPDFConfig   = new FPDFConfig();
    }
}