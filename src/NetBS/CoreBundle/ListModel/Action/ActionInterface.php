<?php

namespace NetBS\CoreBundle\ListModel\Action;

interface ActionInterface
{
    /**
     * Renders the action button corresponding
     * @param object $item
     * @return string
     */
    public function render($item);
}