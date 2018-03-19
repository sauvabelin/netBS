<?php

namespace NetBS\CoreBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use NetBS\CoreBundle\Model\BinderInterface;
use Symfony\Component\Form\Form;

class QueryMaker
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var BinderInterface[]
     */
    protected $binders;

    /**
     * @var int
     */
    protected $aliasIndex   = 0;

    public function __construct(EntityManager $manager)
    {
        $this->manager  = $manager;
    }

    /**
     * @param BinderInterface $binder
     */
    public function registerBinder(BinderInterface $binder) {

        $this->binders[$binder->getType()]  = $binder;
    }

    /**
     * @param $itemClass
     * @param Form $form
     * @return QueryBuilder
     */
    public function buildQuery($itemClass, Form $form) {

        $alias  = '_item';
        $query  = $this->manager->createQueryBuilder()
            ->select($alias)
            ->from($itemClass, $alias);

        $this->concatWith($query, $form, $alias);

        return $query;
    }

    protected function concatWith(QueryBuilder $builder, Form $form, $alias) {

        foreach($form as $item) {

            $data   = $item->getNormData();
            $type   = $item->getConfig()->getType()->getInnerType();
            $class  = get_class($type);

            //Binder
            if(isset($this->binders[$class]))
                $this->binders[$class]->bind($alias, $item, $builder);

            //Children
            elseif($item->count() > 0) {

                $childAlias = $alias . $this->aliasIndex++;
                $wheres     = $this->countWheres($builder);
                $joins      = $builder->getDQLPart('join'); //On sauve les joins d'avant

                $builder->join($alias . '.' . $item->getName(), $childAlias);
                $this->concatWith($builder, $item, $childAlias);

                if($wheres === $this->countWheres($builder)) {

                    $builder->resetDQLPart('join');

                    foreach($joins as $key => $items) {

                        /** @var Join $join */
                        foreach($items as $join) //On les restaure
                            $builder->join($join->getJoin(), $join->getAlias());
                    }
                }
            }

            elseif($data !== null)
                $this->binders["_netbs.equals"]->bind($alias, $item, $builder);
        }
    }

    protected function countWheres(QueryBuilder $builder) {

        $wheres = $builder->getDQLPart('where');

        if($wheres === null)
            return 0;

        return count($wheres->getParts());
    }
}