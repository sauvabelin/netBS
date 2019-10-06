<?php

namespace TenteBundle\Listener;

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
        $this->storage      = $storage;
    }

    public function onMenuConfigure(ExtendMainMenuEvent $event)
    {
        /** @var BSUser $user */
        $menu   = $event->getMenu();
        $tentes = $menu->registerCategory('tentes', 'tentes');
        $user   = $this->storage->getToken()->getUser();

        $tentes->addLink('tentes.dashboard', 'Administration tentes', 'fas fa-campground', 'tente.dashboard');
        $tentes->addLink('tentes.search', 'Rechercher des tentes', 'fas fa-binoculars', 'tente.tente.search');
    }
}
