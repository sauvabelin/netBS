<?php

namespace NetBS\SecureBundle\Listener;

use NetBS\CoreBundle\Event\ExtendMainMenuEvent;
use NetBS\SecureBundle\Mapping\BaseUser;
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
        /** @var BaseUser $user */
        $user       = $this->token->getToken()->getUser();
        $menu       = $event->getMenu();
        $category   = $menu->getCategory('secure.admin');
        $subMenu    = $category->addSubMenu('netbs.secure.admin.users', 'Utilisateurs', 'fas fa-key');

        if($user->hasRole("ROLE_ADMIN")) {

            $subMenu
                ->addSubLink('Gestion', 'netbs.secure.user.list_users')
                ->addSubLink('Nouveau', 'netbs.secure.user.add_user');
        }
    }
}