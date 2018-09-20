<?php

namespace Ovesco\GalerieBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ovesco_galerie');

        $rootNode
            ->children()
                ->scalarNode('mapped_directory')->isRequired()->end()
                ->scalarNode('prefix_directory')->isRequired()->end()
                ->scalarNode('cache_directory')->isRequired()->end()
                ->arrayNode('image_extensions')
                    ->scalarPrototype()->end()
                ->end()
                ->scalarNode('description_filename')->defaultValue("description.md")->end()
            ->end();

        return $treeBuilder;
    }
}
