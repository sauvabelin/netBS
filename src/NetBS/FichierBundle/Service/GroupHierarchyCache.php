<?php

namespace NetBS\FichierBundle\Service;

use Doctrine\ORM\EntityManager;
use NetBS\FichierBundle\Mapping\BaseGroupe;
use Symfony\Component\Cache\Adapter\SimpleCacheAdapter;

class GroupHierarchyCache
{
    protected $config;

    protected $cache;

    protected $manager;

    public function __construct(FichierConfig $config, SimpleCacheAdapter $adapter, EntityManager $manager)
    {
        $this->config   = $config;
        $this->cache    = $adapter;
        $this->manager  = $manager;
    }

    public function buildTree() {

        $root   = $this->manager->getRepository($this->config->getGroupeClass())->findOneBy(array('parent' => null));
        $base   = $this->simplifyGroupe($root);
    }

    public function simplifyGroupe(BaseGroupe $groupe) {


    }
}