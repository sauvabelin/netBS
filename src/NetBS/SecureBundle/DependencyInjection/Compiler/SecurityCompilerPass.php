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
        $definition = new Definition();
        $definition->setClass(AuthorizationHeaderTokenExtractor::class)
            ->setArgument(0, "Bearer")
            ->setArgument(1, "x-authorization");

        $container->setDefinition("netbs.temp.token_extractor", $definition);
        $container->getDefinition("lexik_jwt_authentication.security.authentication.listener")
            ->addMethodCall("addTokenExtractor", [new Reference("netbs.temp.token_extractor")]);
    }
}
