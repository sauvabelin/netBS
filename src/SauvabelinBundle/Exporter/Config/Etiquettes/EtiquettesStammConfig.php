<?php

namespace SauvabelinBundle\Exporter\Config\Etiquettes;

use NetBS\CoreBundle\Model\ExporterConfigInterface;
use NetBS\FichierBundle\Exporter\Config\EtiquettesV2Config;

class EtiquettesStammConfig extends EtiquettesV2Config implements ExporterConfigInterface
{
    public $reperes = false;

    public $horizontalMargin = 12.5;

    public $verticalMargin = 12.5;

    public $rows = 8;

    public $columns = 4;

    public $paddingLeft = 5;

    public $paddingTop = 5;

    public $fontSize = 8;

    public $interligne = 4;

    public $economies = '';

    public $infoPage = false;

    public $mergeFamilles = true;

    public static function getName()
    {
        return "Stamm DN2250";
    }

    public static function getDescription()
    {
        return "Imprimante du stamm";
    }
}
