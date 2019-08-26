<?php

namespace Ovesco\WhatsappBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ScenarioPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $manager = $container->findDefinition('ovesco.whatsapp.service.scenarios_manager');

        foreach ($container->findTaggedServiceIds('ovesco.whatsapp.scenario') as $serviceId => $p) {
            $manager->addMethodCall('registerScenario', [new Reference($serviceId)]);
        }
    }
}
