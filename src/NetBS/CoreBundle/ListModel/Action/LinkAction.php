<?php

namespace NetBS\CoreBundle\ListModel\Action;

class LinkAction implements ActionInterface
{
    protected $route;

    protected $icon;

    protected $theme;

    public function __construct($route, $icon = 'fas fa-edit', $theme = 'secondary')
    {
        $this->route    = $route;
        $this->icon     = $icon;
        $this->theme    = $theme;
    }

    public function render($item)
    {
        $route  = is_string($this->route) ? $this->route : ($this->route)($item);
        return "<a href='$route' class='btn btn-xs btn-{$this->theme}'><i class='{$this->icon}'></i></a>";
    }
}