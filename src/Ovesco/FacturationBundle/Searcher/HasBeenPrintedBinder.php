<?php

namespace Ovesco\FacturationBundle\Searcher;

use NetBS\CoreBundle\Model\BaseBinder;
use Ovesco\FacturationBundle\Entity\Facture;
use Ovesco\FacturationBundle\Form\Type\HasBeenPrintedType;

class HasBeenPrintedBinder extends BaseBinder
{
    public function bindType()
    {
        return self::POST_FILTER;
    }

    public function getType()
    {
        return HasBeenPrintedType::class;
    }

    /**
     * @param Facture $item
     * @param $value
     * @param array $options
     * @return bool
     */
    public function postFilter($item, $value, array $options)
    {
        return $value === 'yes' ? $item->hasBeenPrinted() : !$item->hasBeenPrinted();
    }
}
