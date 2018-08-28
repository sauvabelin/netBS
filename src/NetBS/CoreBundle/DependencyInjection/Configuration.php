<?php

namespace NetBS\CoreBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('netbs_core');

        $rootNode
            ->children()
                ->arrayNode('mailer')
                    ->children()
                        ->scalarNode('subject_prefix')->defaultValue('')->end()
                        ->scalarNode('default_from')->defaultNull()->end()
                        ->arrayNode('channels')
                            ->useAttributeAsKey('alias')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('subject')->defaultNull()->end()
                                    ->scalarNode('from')->defaultNull()->end()
                                    ->scalarNode('template')->isRequired()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
