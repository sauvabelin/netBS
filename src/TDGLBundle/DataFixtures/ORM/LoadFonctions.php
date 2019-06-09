<?php

namespace TDGLBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NetBS\FichierBundle\Service\FichierConfig;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;
use Symfony\Component\Yaml\Yaml;

class LoadFonctions extends ContainerAwareFixture implements OrderedFixtureInterface
{
    protected function loadYAML($file) {

        return Yaml::parseFile(__DIR__ . "/../../Resources/structure/$file");
    }

    public function load(ObjectManager $manager)
    {
        /** @var FichierConfig $config */
        $config = $this->container->get('netbs.fichier.config');
        $data = $this->loadYAML('fonctions.yml');
        $roles  = [];

        /** @var Role $role */
        foreach($manager->getRepository('NetBSSecureBundle:Role')->findAll() as $role)
            $roles[$role->getRole()] = $role;

        foreach($data['fonctions'] as $name => $params) {

            $fonction = $config->createFonction();
            $fonction
                ->setAbbreviation($params['abbr'])
                ->setNom($name)
                ->setPoids($params['poids']);

            if (isset($params['roles']))
                foreach($params['roles'] as $requiredRole)
                    $fonction->addRole($roles[$requiredRole]);

            $manager->persist($fonction);
        }

        $manager->flush();
    }


    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 100;
    }
}
