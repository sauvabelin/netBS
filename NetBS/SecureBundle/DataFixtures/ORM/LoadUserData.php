<?php

namespace NetBS\SecureBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NetBS\SecureBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userClass  = $this->container->get('netbs.secure.config')->getUserClass();

        $user       = new $userClass();
        $user->setUsername('admin');

        $encoder    = $this->container->get('security.password_encoder');
        $password   = $encoder->encodePassword($user, 'password');
        $user->setPassword($password);

        $user->addRole($manager->getRepository('NetBSSecureBundle:Role')->findOneBy(array('role' => 'ROLE_ADMIN')));

        $manager->persist($user);
        $manager->flush();

        $this->addReference('admin', $user);
    }

    public function getOrder()
    {
        return 100;
    }
}