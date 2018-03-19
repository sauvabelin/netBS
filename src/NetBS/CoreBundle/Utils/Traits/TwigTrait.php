<?php

namespace NetBS\CoreBundle\Utils\Traits;

trait TwigTrait
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function setTwig(\Twig_Environment $twig_Environment) {

        $this->twig = $twig_Environment;
    }
}