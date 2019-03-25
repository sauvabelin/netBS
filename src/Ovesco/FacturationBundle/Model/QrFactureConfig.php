<?php

namespace Ovesco\FacturationBundle\Model;

use NetBS\CoreBundle\Exporter\Config\FPDFConfig;

class QrFactureConfig extends FPDFConfig
{
    public $border = true;

    public $model = null;

    public $date;

    public function __construct()
    {
        $this->date = new \DateTime();
    }
}
