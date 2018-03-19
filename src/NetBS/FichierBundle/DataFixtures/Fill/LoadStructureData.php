<?php

namespace NetBS\FichierBundle\DataFixtures\Fill;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NetBS\FichierBundle\Entity\Distinction;
use NetBS\FichierBundle\Entity\Fonction;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadStructureData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container    = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $config         = $this->container->get('netbs.fichier.config');

        $distinctions   = ['Badge feu', 'Badge cuistot', 'Badge recycleur', 'Betterave de qualité', 'Culture BIO'];
        foreach($distinctions as $k => $distinction) {

            $dist   = $config->createDistinction($distinction);
            $manager->persist($dist);

            $this->addReference('d' . $k, $dist);
        }

        $fonctions      = [
            'chef de patrouille'    => [10, 'CP'],
            'chef de troupe'        => [100, 'CT'],
            'gars ou fille'         => [1, 'gars'],
            'chef de branche'       => [300, 'CB'],
            'commandant'            => [1000, 'Cdt'],
            'sous-cp'               => [8, 'sCP']
        ];

        foreach($fonctions as $nom => $d) {

            $fonction = $config->createFonction();
            $fonction->setNom($nom)->setPoids($d[0])->setAbbreviation($d[1]);

            $manager->persist($fonction);
            $manager->flush();

            $this->addReference($d[1], $fonction);
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1000;
    }
}