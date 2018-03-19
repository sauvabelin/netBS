<?php

namespace NetBS\ListBundle\Column;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeColumn extends BaseColumn
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('format', 'd.m.Y');
    }

    /**
     * Return content related to the given object with the given params
     * @param object $item
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function getContent($item, array $params = [])
    {
        if($item === null)
            return '';

        if($item instanceof \DateTime)
            return $item->format($params['format']);

        throw new \Exception("Object is not a DateTime!");
    }
}
