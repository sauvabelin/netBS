<?php

namespace Ovesco\GalerieBundle\DependencyInjection;

use Ovesco\GalerieBundle\Model\GalerieConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class OvescoGalerieExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setDefinition('ovesco.galerie.config', new Definition(GalerieConfig::class, [
            $container->getParameter("kernel.project_dir"),
            $config['mapped_directory'],
            $config['cache_directory'],
            $config['image_extensions'],
            $config['description_filename']
        ]));
    }
}
