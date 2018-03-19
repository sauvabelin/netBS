<?php

namespace NetBS\SecureBundle\Listener;

use NetBS\CoreBundle\Event\ExtendMainMenuEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class MainMenuListener
{
    /**
     * @var TokenStorage
     */
    protected $token;

    public function __construct(TokenStorage $storage)
    {
        $this->token    = $storage;
    }

    public function onMenuConfigure(ExtendMainMenuEvent $event)
    {
        $menu       = $event->getMenu();
        $category   = $menu->getCategory('secure.admin');

        $category->addSubMenu('netbs.secure.admin.users', 'Utilisateurs', 'fas fa-key')
            ->addSubLink('Gestion', 'netbs.secure.user.list_users')
            ->addSubLink('Nouveau', 'netbs.secure.user.add_user');
    }
}