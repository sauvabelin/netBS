<?php

namespace NetBS\CoreBundle\Service;

use Doctrine\ORM\EntityManager;
use NetBS\CoreBundle\Entity\Parameter;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class ParameterManager
{
    const CACHE_KEY = 'netbs.core.params';

    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var AdapterInterface
     */
    protected $cache;

    public function __construct(EntityManager $manager, AdapterInterface $cache)
    {
        $this->manager  = $manager;
        $this->cache    = $cache;
    }

    public function getParameter($namespace, $key) {

        return $this->manager->getRepository('NetBSCoreBundle:Parameter')->findOneBy(array(
            'namespace' => $namespace,
            'paramKey'  => $key
        ));
    }

    public function getValue($namespace, $key) {

        $path       = $this->getCachePath($namespace, $key);
        $item       = $this->cache->getItem($path);

        if(!$item->isHit())
            $item   = $this->cacheParameter($this->getParameter($namespace, $key));

        return $item->get();
    }

    public function refresh() {

        $parameters = $this->manager->getRepository('NetBSCoreBundle:Parameter')->findAll();

        foreach ($parameters as $parameter)
            $this->cacheParameter($parameter);
    }

    protected function cacheParameter(Parameter $parameter) {

        $path   = $this->getCachePath($parameter->getNamespace(), $parameter->getKey());
        $item   = $this->cache->getItem($path);

        $item->set($parameter->getValue());
        $this->cache->save($item);

        return $item;
    }

    protected function getCachePath($namespace, $key) {

        return self::CACHE_KEY . '.' . $namespace . '.' . $key;
    }
}