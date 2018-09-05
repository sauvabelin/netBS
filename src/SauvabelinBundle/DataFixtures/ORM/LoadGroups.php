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

        $manager->flush();

        $catUpdates = [
            'groupe_categorie.sous_unite_id'    => 'sous-unité',
            'groupe_categorie.unite_id'         => "unité"
        ];

        foreach($catUpdates as $key => $fn)
            $this->loadParameterWithId($manager, 'bs', $key,
                $manager->getRepository('NetBSFichierBundle:GroupeCategorie')->findOneBy(array('nom' => $fn)));

        foreach($data['types'] as $name => $type) {

            $item   = $config->createGroupeType();
            $item->setNom($name);
            $item->setAffichageEffectifs($type['effectifs'] === 1);
            $item->setGroupeCategorie($categories[$type['cat']]);

            $manager->persist($item);
            $types[$name] = $item;
        }

        $manager->flush();

        $typesUpdates   = [
            'groupe_type.troupe_id'         => 'troupe',
            'groupe_type.meute_id'          => 'meute',
            'groupe_type.clan_id'           => 'clan',
            'groupe_type.association_id'    => 'association',
            'groupe_type.edc_id'            => 'équipe de commandement',
            'groupe_type.equipe_interne_id' => 'équipe interne',
        ];

        foreach($typesUpdates as $key => $fn)
            $this->loadParameterWithId($manager, 'bs', $key,
                $manager->getRepository('NetBSFichierBundle:GroupeType')->findOneBy(array('nom' => $fn)));

        $this->mapGroup($manager, $config, $types, $data['unites']);
        $manager->flush();

        $updates    = [
            'groupe.branche_eclaireurs_id'      => "branche éclaireurs",
            'groupe.branche_eclaireuses_id'     => "branche éclaireuses",
            'groupe.branche_louveteaux_id'      => "branche louveteaux",
            'groupe.branche_louvettes_id'       => "branche louvettes"
        ];

        foreach($updates as $key => $fn)
            $this->loadParameterWithId($manager, 'bs', $key,
                $manager->getRepository('SauvabelinBundle:BSGroupe')->findOneBy(array('nom' => $fn)));
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