<?php

namespace NetBS\CoreBundle\Twig\Extension;

use NetBS\CoreBundle\Service\ParameterManager;

class ParamExtension extends \Twig_Extension
{
    private $params;

    public function __construct(ParameterManager $manager)
    {
        $this->params   = $manager;
    }

    public function getFunctions() {

        return [
            new \Twig_SimpleFunction('param', [$this, 'getParameter'])
        ];
    }

    public function getParameter($namespace, $key) {

        return $this->params->getValue($namespace, $key);
    }
}
