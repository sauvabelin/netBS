<?php

namespace NetBS\ListBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ListExtension extends AbstractExtension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container    = $container;
    }

    public function getFunctions() {

        return [
            new TwigFunction('render_list', array($this, 'renderListFunction'), array('is_safe' => array('html')))
        ];
    }

    public function renderListFunction($list, $renderer, array $params = []) {

        return $this->container->get('netbs.list.engine')->render($list, $renderer, $params)->getContent();
    }
}
