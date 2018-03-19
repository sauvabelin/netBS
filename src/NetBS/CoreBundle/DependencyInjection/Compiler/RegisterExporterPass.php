<?php

namespace NetBS\CoreBundle\DependencyInjection\Compiler;

use NetBS\CoreBundle\Exporter\CSVExporter;
use NetBS\CoreBundle\Exporter\PDFExporter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RegisterExporterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $manager    = $container->getDefinition('netbs.core.exporter_manager');

        foreach($container->findTaggedServiceIds('netbs.exporter') as $serviceId => $params) {

            $definition = $container->getDefinition($serviceId);
            $class      = $definition->getClass();

            if(is_subclass_of($class, CSVExporter::class)) {

                $definition->addMethodCall('setAccessor', [new Reference('property_accessor')]);
            }

            if(is_subclass_of($class, PDFExporter::class)) {

                $definition->addMethodCall('setTwig', [new Reference('twig')])
                           ->addMethodCall('setSnappy', [new Reference('knp_snappy.pdf')]);
            }

            $manager->addMethodCall('registerExporter', [new Reference($serviceId)]);
        }
    }
}
