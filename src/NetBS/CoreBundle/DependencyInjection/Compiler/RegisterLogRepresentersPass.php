<?php

namespace NetBS\CoreBundle\DependencyInjection\Compiler;

use NetBS\FichierBundle\LogRepresenter\FichierRepresenter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RegisterLogRepresentersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $manager    = $container->getDefinition('netbs.core.logger_manager');
        $manager->addMethodCall('setEntityManager', [new Reference('doctrine.orm.entity_manager')]);

        foreach($container->findTaggedServiceIds('netbs.log_representer') as $serviceId => $params) {
            $manager->addMethodCall('registerRepresenter', [new Reference($serviceId)]);

            $definition = $container->getDefinition($serviceId);

            if(is_subclass_of($definition->getClass(), FichierRepresenter::class)) {
                $definition->addMethodCall('setConfig', [new Reference('netbs.fichier.config')]);
                $definition->addMethodCall('setTwig', [new Reference('twig')]);
            }
        }
    }
}
