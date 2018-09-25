<?php

namespace SauvabelinBundle\DependencyInjection\Compiler;

use SauvabelinBundle\ListModel\BSUserList;
use SauvabelinBundle\LogRepresenter\MembreRepresenter;
use SauvabelinBundle\Searcher\BSMembreSearcher;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class OverrideServicePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('netbs.fichier.log_representer.membre')->setClass(MembreRepresenter::class);
        $container->getDefinition('netbs.secure.list.users')->setClass(BSUserList::class);
        $container->getDefinition('netbs.fichier.searcher.membres')->setClass(BSMembreSearcher::class);
    }
}
