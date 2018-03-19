<?php

namespace NetBS\FichierBundle\LogRepresenter;

use NetBS\CoreBundle\Model\LogRepresenterInterface;
use NetBS\FichierBundle\Service\FichierConfig;

abstract class FichierRepresenter implements LogRepresenterInterface
{
    /**
     * @var FichierConfig
     */
    protected $config;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function setConfig(FichierConfig $config) {

        $this->config   = $config;
    }

    public function setTwig(\Twig_Environment $twig) {

        $this->twig = $twig;
    }
}