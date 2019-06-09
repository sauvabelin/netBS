<?php

namespace TDGLBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NetBS\FichierBundle\Service\FichierConfig;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;
use Symfony\Component\Yaml\Yaml;

class LoadUnites extends ContainerAwareFixture implements OrderedFixtureInterface
{
    protected function loadYAML($file) {

        return Yaml::parseFile(__DIR__ . "/../../Resources/structure/$file");
    }

    public function load(ObjectManager $manager)
    {
        /** @var FichierConfig $config */
        $config = $this->container->get('netbs.fichier.config');
        $data = $this->loadYAML('unites.yml');
        $categories = [];
        $types = [];

        foreach($data['categories'] as $category) {

            $item   = $config->createGroupeCategorie($category);
            $manager->persist($item);
            $categories[$category] = $item;
        }

        $manager->flush();


        foreach($data['types'] as $name => $values) {

            $item   = $config->createGroupeType();
            $item->setNom($name);
            $item->setAffichageEffectifs($values['se'] === 1);
            $item->setGroupeCategorie($categories[$values['categorie']]);

            $manager->persist($item);
            $types[($name)] = $item;
        }

        $manager->flush();

        $this->mapGroup($manager, $config, $types, $data['unites']);
        $manager->flush();
    }

    private function mapGroup(ObjectManager $manager, FichierConfig $config, array $types, array $data) {

        $groups = [];

        foreach($data as $name => $unitData) {

            $group  = $config->createGroupe();
            $group->setNom($name);
            $group->setGroupeType($types[($unitData['type'])]);
            $manager->persist($group);

            if(isset($unitData['enfants'])) {
                $enfants = $this->mapGroup($manager, $config, $types, $unitData['enfants']);

                foreach ($enfants as $child)
                    $group->addEnfant($child);
            }

            $groups[] = $group;
        }

        return $groups;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 5000;
    }
}
