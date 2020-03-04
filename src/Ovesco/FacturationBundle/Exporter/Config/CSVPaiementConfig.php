<?php

namespace Ovesco\FacturationBundle\Exporter\Config;

use NetBS\CoreBundle\Model\ExporterConfigInterface;

class CSVPaiementConfig implements ExporterConfigInterface
{
    public $compte = true;

    public $creances = true;

    public $montantFacture = true;

    /**
     * @return string
     */
    public static function getName()
    {
        return "Par défaut";
    }

    /**
     * @return string|null
     */
    public static function getDescription()
    {
        return null;
    }
}
