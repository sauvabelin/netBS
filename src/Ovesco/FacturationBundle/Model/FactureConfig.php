<?php

namespace Ovesco\FacturationBundle\Model;

use NetBS\CoreBundle\Exporter\Config\FPDFConfig;

class FactureConfig extends FPDFConfig
{
    public $model = null;
    public $wg = 8; // marge gauche BVR
    public $hg = 248;// ligne codage gauche
    public $haddr = 190; // décalage hauteur adresses du haut
    public $waddr = 56; // décalage gauche adresse haut droite
    public $wccp = 77; // position X du CCP
    public $hccp = 221; // position Y du CCP
    public $wd = 115; // ligne codage droite
    public $hd = 212; // ligne codage droite
    public $wb = 83; // ligne codage bas
    public $hb = 266; // ligne codage bas
    public $bvrIl = 4;
    public $date = null;

    public function __construct()
    {
        $this->margeHaut = 10;
        $this->margeGauche = 12.3;
    }
}
