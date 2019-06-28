<?php

namespace NetBS\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RegisterLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $manager    = $container->getDefinition('netbs.core.loader_manager');

        foreach($container->findTaggedServiceIds('netbs.loader') as $serviceId => $params) {
            $manager->addMethodCall('pushHelper', [new Reference($serviceId)]);
        }

    }
}
