<?php

namespace SauvabelinBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NetBS\FichierBundle\Entity\Distinction;

class LoadDistinctions extends BSFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $distinctions   = $this->loadYAML('distinctions.yml');

        foreach($distinctions['distinctions'] as $name)
            $manager->persist(new Distinction($name));

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 300;
    }
}