<?php

namespace Ovesco\FacturationBundle\Searcher;

use NetBS\CoreBundle\Model\BaseBinder;
use Ovesco\FacturationBundle\Form\Type\CountSearchType;
use Symfony\Component\PropertyAccess\PropertyAccess;

class CountBinder extends BaseBinder
{
    private $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function bindType()
    {
        return self::POST_FILTER;
    }

    public function getType()
    {
        return CountSearchType::class;
    }

    public function postFilter($item, $value, array $options)
    {
        $items = $this->propertyAccessor->getValue($item, $options['property']);
        return count($items) === intval($value);
    }
}