<?php

namespace GalerieBundle\Listener;

use NetBS\CoreBundle\Event\ExtendMainMenuEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class MainMenuListener
{
    /**
     * @var TokenStorage
     */
    private $storage;

    public function __construct(TokenStorage $storage)
    {
        $this->storage  = $storage;
    }

    public function onMenuConfigure(ExtendMainMenuEvent $event)
    {
        $menu       = $event->getMenu();
        $category   = $menu->getCategory('other');
        $submenu    = $category->addSubMenu('other.gallery', 'Galerie', 'fas fa-images');

        $submenu->addSubLink("Administration", "netbs.galerie.admin.dashboard");
        $submenu->addSubLink("Gestion des dossiers", "netbs.galerie.admin.directories");
    }
}
