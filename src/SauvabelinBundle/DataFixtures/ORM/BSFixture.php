<?php

namespace SauvabelinBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

abstract class BSFixture extends ContainerAwareFixture implements OrderedFixtureInterface
{
    protected function loadYAML($file) {

        return Yaml::parseFile(__DIR__ . "/../../Resources/structure/$file");
    }

    protected function loadParameterWithId(ObjectManager $manager, $namespace, $paramn, $item) {

        if(!$item || !method_exists($item, 'getId'))
            throw new \Exception("Tried to update param $namespace.$paramn with an invalid object");

        $param  = $manager->getRepository('NetBSCoreBundle:Parameter')->findOneBy(array(
            'namespace' => $namespace,
            'paramKey'  => $paramn
        ));

        $param->setValue($item->getId());
        $manager->persist($param);
        $manager->flush();
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer() {

        return $this->container;
    }

    public function getOrder()
    {
        return 100;
    }
}