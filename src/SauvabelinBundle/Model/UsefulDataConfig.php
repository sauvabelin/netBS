<?php

namespace SauvabelinBundle\Model;

use NetBS\CoreBundle\Model\ExporterConfigInterface;

class UsefulDataConfig implements ExporterConfigInterface
{
    /**
     * @var bool
     */
    public $cravateBleue;

    /**
     * @return string
     */
    public static function getName()
    {
        return "Par défaut";
    }

    /**
     * @return string|null
     */
    public static function getDescription()
    {
        return null;
    }
}
