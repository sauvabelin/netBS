<?php

namespace Ovesco\FacturationBundle\Listener;

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
        $user   = $this->storage->getToken()->getUser();
        if (!$user->hasRole('ROLE_TRESORIER')) return;
        $category = $menu->registerCategory('ovesco.facturation', 'Facturation');

        $category->addLink('facturation.dashboard', 'Administration', 'fas fa-money-bill-alt', 'ovesco.facturation.dashboard');
        $search = $category->addSubMenu('facturation.search', 'Rechercher', 'fas fa-search');
        $search->addSubLink('Créances', 'ovesco.facturation.search_creances');
        $search->addSubLink('Factures', 'ovesco.facturation.search_factures');
    }
}
