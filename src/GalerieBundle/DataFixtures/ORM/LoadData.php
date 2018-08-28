<?php

namespace GalerieBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;

class LoadData extends ContainerAwareFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var ContainerInterface $container */
        $container  = $this->container;
        $config     = $container->get('netbs.secure.config');
        $role       = $config->createRole();

        $role->setParent($manager->getRepository($config->getRoleClass())->findOneBy(array('role' => 'ROLE_SG')));
        $role->setRole("ROLE_MANAGE_GALERIE");
        $role->setDescription("Gestion de la galerie côté fichier");
        $role->setPoids(80);

        $manager->persist($role);
        $manager->flush();
    }

    public function getOrder()
    {
        return 310;
    }
}