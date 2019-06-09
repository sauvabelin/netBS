<?php

namespace TDGLBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use TDGLBundle\ListModel\TDGLUserList;
use TDGLBundle\Searcher\TDGLMembreSearcher;

class ServiceOverride implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('netbs.fichier.searcher.membres')->setClass(TDGLMembreSearcher::class);
        $container->getDefinition('netbs.secure.list.users')->setClass(TDGLUserList::class);
    }
}
