<?php

namespace NetBS\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RegisterPostInstallScriptsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $manager    = $container->getDefinition('netbs.core.post_install_script_manager');

        foreach($container->findTaggedServiceIds('netbs.post_install_script') as $serviceId => $params)
            $manager->addMethodCall('registerScript', [new Reference($serviceId)]);
    }
}
