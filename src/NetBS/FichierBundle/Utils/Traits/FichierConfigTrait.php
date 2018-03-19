<?php

namespace NetBS\FichierBundle\Utils\Traits;

use NetBS\FichierBundle\Service\FichierConfig;

trait FichierConfigTrait
{
    /**
     * @var FichierConfig
     */
    protected $config;

    public function setFichierConfig(FichierConfig $config) {

        $this->config   = $config;
    }

    public function getFichierConfig() {

        return $this->config;
    }
}