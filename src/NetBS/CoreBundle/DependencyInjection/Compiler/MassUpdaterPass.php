<?php

namespace NetBS\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class MassUpdaterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $manager    = $container->getDefinition('netbs.core.mass_updater_manager');

        foreach($container->findTaggedServiceIds('netbs.mass_updater') as $id => $params)
            $manager->addMethodCall('registerUpdater', [new Reference($id)]);
    }
}
