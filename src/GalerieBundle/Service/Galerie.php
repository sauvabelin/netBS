<?php

namespace GalerieBundle\Service;

use NetBS\CoreBundle\Service\ParameterManager;

class Galerie
{
    private $params;

    public function __construct(ParameterManager $params)
    {
        $this->params   = $params;
    }

    public function getToken() {

        return $this->params->getValue('galerie', 'access_token');
    }

    public function setToken($token) {

        $this->params->setValue('galerie', 'access_token', $token);
    }
}