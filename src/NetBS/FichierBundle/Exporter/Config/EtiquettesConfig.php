<?php

namespace NetBS\FichierBundle\Exporter\Config;

use NetBS\CoreBundle\Exporter\Config\FPDFConfig;

class EtiquettesConfig
{
    public $FPDFConfig;

    public $margeInferieure             = 5;

    public $margeDroite                 = 0;

    public $colonnes                    = 3;

    public $lignes                      = 8;

    public $taillePolice                = 10;

    public $margeInterieureVerticale    = 5;

    public $margeInterieureHorizontale  = 5;

    public $showInfoPage                = false;

    public $title                       = "Aux parents de";

    public $mergeFamilles               = true;

    public function __construct()
    {
        $this->FPDFConfig   = new FPDFConfig();
    }
}
