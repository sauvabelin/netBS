<?php

namespace NetBS\ListBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ListExtension extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container    = $container;
    }

    public function getFunctions() {

        return [
            new \Twig_SimpleFunction('render_list', array($this, 'renderListFunction'), array('is_safe' => array('html')))
        ];
    }

    public function renderListFunction($list, $renderer, array $params = []) {

        return $this->container->get('netbs.list.engine')->render($list, $renderer, $params)->getContent();
    }
}