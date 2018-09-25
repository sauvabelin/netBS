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
        $data   = $form->getNormData();
        $field  = $alias . "." . $config->getName();
        $param  = '_param' . $this->count++;

        if(strpos("%", $data) !== false)
            $builder->andWhere($builder->expr()->like($field, ':' . $param));
        else
            $builder->andWhere($builder->expr()->eq($field, ':' . $param));

        $builder
            ->setParameter($param, $data);
    }

    public function getType()
    {
        return "_netbs.equals";
    }
}