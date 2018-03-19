<?php

namespace NetBS\FichierBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NetBS\SecureBundle\Entity\Role;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class LoadRolesData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container    = $container;
    }

    public function load(ObjectManager $manager)
    {
        $config = Yaml::parse(file_get_contents(__DIR__ . "/../../Resources/security/roles.yml"));
        $roles  = $this->loadRole($config['roles'], $manager);

        foreach($roles as $role) {

            $role->setParent($this->getReference('ROLE_ADMIN'));
            $manager->persist($role);
        }

        $manager->flush();
    }

    public function loadRole(array $data, ObjectManager $manager) {

        $roleClass  = $this->container->get('netbs.secure.config')->getRoleClass();

        $roles  = [];

        foreach($data as $name => $params) {

            $role   = new $roleClass($name, $params['poids'], $params['description']);

            if(isset($params['children'])) {

                $childs = $this->loadRole($params['children'], $manager);

                foreach($childs as $child)
                    $role->addChild($child);
            }

            $manager->persist($role);
            $roles[] = $role;
        }

        return $roles;
    }

    public function getOrder()
    {
        return 300;
    }
}