<?php

namespace NetBS\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RegisterLayoutPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('netbs.core.block.layout');

        foreach($container->findTaggedServiceIds('netbs.block.layout') as $id => $p)
            $definition->addMethodCall('registerLayout', [new Reference($id)]);
    }
}
