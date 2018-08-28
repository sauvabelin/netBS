<?php

namespace GalerieBundle\Listener;

use NetBS\CoreBundle\Event\ExtendMainMenuEvent;
use SauvabelinBundle\Entity\BSUser;
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
        /** @var BSUser $user */
        $user       = $this->storage->getToken()->getUser();

        if($user->hasRole("ROLE_MANAGE_GALERIE")) {

            $menu = $event->getMenu();
            $category = $menu->getCategory('other');
            $submenu = $category->addSubMenu('other.gallery', 'Galerie', 'fas fa-images');

            $submenu->addSubLink("Administration", "netbs.galerie.admin.dashboard");
        }
    }
}
