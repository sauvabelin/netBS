<?php

namespace NetBS\CoreBundle\Model\Helper;

use Symfony\Component\Routing\Router;

abstract class BaseHelper implements HelperInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var Router
     */
    protected $router;

    public function setTwig(\Twig_Environment $twig_Environment) {

        $this->twig = $twig_Environment;
    }

    public function setRouter(Router $router) {

        $this->router   = $router;
    }
}