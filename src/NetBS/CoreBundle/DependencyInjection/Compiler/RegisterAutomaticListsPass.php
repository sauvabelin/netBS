<?php

namespace NetBS\CoreBundle\DependencyInjection\Compiler;

use NetBS\CoreBundle\Model\ConfigurableAutomaticInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RegisterAutomaticListsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $manager    = $container->getDefinition('netbs.core.automatic_lists_manager');

        foreach($container->findTaggedServiceIds('netbs.automatic_list') as $id => $params) {

            $class = $container->getDefinition($id)->getClass();
            if(!in_array(ConfigurableAutomaticInterface::class, class_implements($class)))
                throw new \Exception("Automatic list $id must implement AutomaticListInterface !");

            $manager->addMethodCall('registerAutomatic', [new Reference($id)]);
        }
    }
}
