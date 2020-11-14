<?php

namespace NetBS\FichierBundle\DataFixtures\Fill;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NetBS\FichierBundle\Mapping\BaseGeniteur;
use NetBS\FichierBundle\Mapping\Personne;
use NetBS\FichierBundle\Service\FichierConfig;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadFamilleData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $config     = $this->container->get('netbs.fichier.config');
        $f1         = $this->loadF1($manager, $config);
        $f2         = $this->loadF2($manager, $config);

        $manager->flush();

        $this->addReference('famille1', $f1);
        $this->addReference('famille2', $f2);
    }

    public function loadF1(ObjectManager $manager, FichierConfig $config) {

        $adresse    = $config->createAdresse();
        $adresse->setRue('Chemin des cultures de beterave')
            ->setNpa('1012')
            ->setLocalite("Port du Havre")
            ->setExpediable(true);

        $mere       = $config->createGeniteur();
        $mere->setStatut(BaseGeniteur::MERE)->setProfession("Cultivatrice")->setPrenom('Mama')
            ->setSexe(Personne::FEMME);

        $famille    = $config->createFamille();
        $famille->setNom('Brown')
            ->setRemarques("Famille exemple")
            ->addAdresse($adresse)
            ->addGeniteur($mere);

        $manager->persist($famille);

        return $famille;
    }

    public function loadF2(ObjectManager $manager, FichierConfig $config) {

        $famille    = $config->createFamille()->setNom('Olive');
        $geniteur   = $config->createGeniteur()
            ->setStatut(BaseGeniteur::GRAND_PARENT)
            ->setSexe(Personne::HOMME)
            ->setPrenom('Jean-Eude');
        $famille->addGeniteur($geniteur);

        $adresse    = $config->createAdresse()->setRue('Chemin des poiriers')->setNpa('2002')->setLocalite('Salt lake city');
        $geniteur->addAdresse($adresse);
        $geniteur->addEmail($config->createEmail('marc.olive@bluewin.ch'));

        $famille->addTelephone($config->createTelephone('0234454433'));
        $manager->persist($famille);

        return $famille;
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