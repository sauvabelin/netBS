<?php

namespace NetBS\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RegisterSearcherPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $manager    = $container->getDefinition('netbs.core.searcher_manager');
        $binder     = $container->getDefinition('netbs.core.query_maker');

        foreach($container->findTaggedServiceIds('netbs.searcher') as $serviceId => $params) {

            $searcher   = $container->getDefinition($serviceId);

            $searcher->addMethodCall('setQueryMaker', [new Reference('netbs.core.query_maker')]);
            $searcher->addMethodCall('setParameterManager', [new Reference('netbs.params')]);
            $manager->addMethodCall('registerSearcher', [new Reference($serviceId)]);
        }

        foreach($container->findTaggedServiceIds('netbs.searcher.binder') as $serviceId => $p)
            $binder->addMethodCall('registerBinder', [new Reference($serviceId)]);
    }
}
