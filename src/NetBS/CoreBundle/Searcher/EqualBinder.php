<?php

namespace NetBS\CoreBundle\Searcher;

use Doctrine\ORM\QueryBuilder;
use NetBS\CoreBundle\Model\BinderInterface;
use Symfony\Component\Form\Form;

class EqualBinder implements BinderInterface
{
    const KEY        = 'netbs.equals';
    protected $count = 0;

    public function bind($alias, Form $form, QueryBuilder $builder)
    {
        $config = $form->getConfig();
        $field  = $alias . "." . $config->getName();
        $param  = '_param' . $this->count++;

        $builder->andWhere($builder->expr()->eq($field, ':' . $param))
            ->setParameter($param, $form->getNormData());
    }

    public function getType()
    {
        return "_netbs.equals";
    }
}