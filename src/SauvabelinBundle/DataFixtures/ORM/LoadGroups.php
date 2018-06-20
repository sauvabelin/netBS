<?php

namespace SauvabelinBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NetBS\FichierBundle\Service\FichierConfig;

class LoadGroups extends BSFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var FichierConfig $config */
        $config         = $this->getContainer()->get('netbs.fichier.config');
        $data           = $this->loadYAML('groups.yml');
        $categories     = [];
        $types          = [];

        foreach($data['categories'] as $category) {

            $item   = $config->createGroupeCategorie($category);
            $manager->persist($item);
            $categories[$category] = $item;
        }

        foreach($data['types'] as $name => $type) {

            $item   = $config->createGroupeType();
            $item->setNom($name);
            $item->setAffichageEffectifs($type['effectifs'] === 1);
            $item->setGroupeCategorie($categories[$type['cat']]);

            $manager->persist($item);
            $types[$name] = $item;
        }

        $this->mapGroup($manager, $config, $types, $data['unites']);
        $manager->flush();
    }

    private function mapGroup(ObjectManager $manager, FichierConfig $config, array $types, array $data) {

        $groups = [];

        foreach($data as $name => $unitData) {

            $group  = $config->createGroupe();
            $group->setNom($name);
            $group->setGroupeType($types[$unitData['model']]);
            $manager->persist($group);

            if(isset($unitData['children'])) {
                $enfants = $this->mapGroup($manager, $config, $types, $unitData['children']);

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