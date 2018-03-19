<?php

namespace NetBS\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RegisterDynamicsModel implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $manager        = $container->getDefinition('netbs.core.dynamic_list_manager');
        $bridges        = $container->getDefinition('netbs.core.bridge_manager');

        foreach($container->findTaggedServiceIds('netbs.dynamic_model') as $id => $params)
            $manager->addMethodCall('registerModel', [new Reference($id)]);


        foreach($container->findTaggedServiceIds('netbs.bridge') as $id => $params)
            $bridges->addMethodCall('registerBridge', [new Reference($id)]);

        $bridges->addMethodCall('buildGraph');
    }
}
