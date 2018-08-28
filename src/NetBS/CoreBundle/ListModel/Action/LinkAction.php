<?php

namespace NetBS\CoreBundle\ListModel\Action;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;

class LinkAction implements ActionInterface
{
    const TEXT      = 'text';
    const THEME     = 'theme';
    const ROUTE     = 'route';
    const SIZE      = 'size';
    const CLASSE    = 'class';
    const ATTRS     = 'attrs';
    const TAG       = 'tag';

    protected $router;

    public function __construct(Router $router)
    {
        $this->router   = $router;
    }

    public function configureOptions(OptionsResolver $resolver) {

        $resolver->setDefault('text', 'button')
            ->setDefault(self::THEME, 'secondary')
            ->setDefault(self::SIZE, 'btn-xs')
            ->setDefault(self::CLASSE, '')
            ->setDefault(self::TAG, 'a')
            ->setDefault(self::ATTRS, '')
            ->setRequired(self::ROUTE);
    }

    public function render($item, $params = [])
    {
        $route  = is_string($params[self::ROUTE]) ? $params[self::ROUTE] : ($params[self::ROUTE])($item);
        $href   = $params[self::TAG] === 'a' ? "href='$route'" : "";

        return "<{$params[self::TAG]} $href {$params[self::ATTRS]} class='btn {$params[self::SIZE]}"
            . " btn-{$params[self::THEME]} {$params[self::CLASSE]}'>{$params[self::TEXT]}</{$params[self::TAG]}>";
    }
}