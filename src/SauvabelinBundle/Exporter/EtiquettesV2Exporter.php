<?php

namespace SauvabelinBundle\Exporter;

use NetBS\FichierBundle\Exporter\Config\EtiquettesV2Config;
use NetBS\FichierBundle\Exporter\PDFEtiquettesV2;
use SauvabelinBundle\Exporter\Config\Etiquettes\EtiquettesStammConfig;

class EtiquettesV2Exporter extends PDFEtiquettesV2
{
    public function getBasicConfig()
    {
        return [
            new EtiquettesV2Config(),
            new EtiquettesStammConfig(),
        ];
    }
}
