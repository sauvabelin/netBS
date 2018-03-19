<?php

namespace NetBS\CoreBundle\ListModel\Column;

use NetBS\CoreBundle\Service\HelperManager;
use NetBS\CoreBundle\Twig\Extension\HelperExtension;
use NetBS\ListBundle\Column\BaseColumn;

class HelperColumn extends BaseColumn
{
    protected $helperManager;

    protected $helperExtension;

    public function __construct(HelperExtension $extension, HelperManager $manager)
    {
        $this->helperExtension  = $extension;
        $this->helperManager    = $manager;
    }

    /**
     * Return content related to the given object with the given params
     * @param object $item
     * @param array $params
     * @return string
     */
    public function getContent($item, array $params = [])
    {
        if(!$item)
            return "";

        $helper = $this->helperManager->getFor($item);

        $label  = $helper->getRepresentation($item);
        $path   = $helper->getRoute($item);
        $attr   = $this->helperExtension->generateHelperAttribute($item);

        if($path)
            return "<a href='$path' $attr>$label</a>";
        else
            return "<span $attr>$label</span>";
    }
}