<?php

namespace NetBS\CoreBundle\Model;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Form;

interface BinderInterface
{
    public function getType();

    public function bind($alias, Form $form, QueryBuilder $builder);
}