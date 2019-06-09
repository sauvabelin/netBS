<?php

namespace NetBS\SecureBundle\DependencyInjection\Compiler;

use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class SecurityCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
    }
}
