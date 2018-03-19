<?php

namespace NetBS\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NetBS\CoreBundle\Entity\Parameter;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class LoadParametersData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function load(ObjectManager $manager)
    {
        $bundles    = $this->container->get('kernel')->getBundles();

        foreach($bundles as $bundle) {

            $path = $bundle->getPath() . "/Resources/config/parameters.yml";

            if (!file_exists($path))
                continue;

            $params = Yaml::parse(file_get_contents($path));

            foreach($params as $namespace => $parameters) {

                foreach ($parameters as $key => $value) {

                    $param = $manager->getRepository('NetBSCoreBundle:Parameter')->findOneBy(array(
                        'namespace' => $namespace,
                        'paramKey'  => $key
                    ));

                    if(!$param)
                        $param = new Parameter($namespace, $key, $value);
                    else
                        $param->setValue($value);

                    $manager->persist($param);
                }
            }
        }

        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container    = $container;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 10;
    }
}