<?php

namespace SauvabelinBundle\DependencyInjection\Compiler;

use Ovesco\GalerieBundle\Imagine\GalerieLoader;
use SauvabelinBundle\ListModel\BSUserList;
use SauvabelinBundle\LogRepresenter\MembreRepresenter;
use SauvabelinBundle\Searcher\BSMembreSearcher;
use SauvabelinBundle\Service\UserManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class OverrideServicePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('netbs.fichier.log_representer.membre')->setClass(MembreRepresenter::class);
        $container->getDefinition('netbs.secure.list.users')->setClass(BSUserList::class);
        $container->getDefinition('netbs.fichier.searcher.membres')->setClass(BSMembreSearcher::class);
        $container->getDefinition('netbs.secure.user_manager')->setClass(UserManager::class);
        $container->getDefinition('liip_imagine.binary.loader.prototype.filesystem')->setClass(GalerieLoader::class);
    }
}
