<?php

namespace NetBS\SecureBundle;

use NetBS\SecureBundle\DependencyInjection\Compiler\SecurityCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NetBSSecureBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new SecurityCompilerPass());
    }
}
