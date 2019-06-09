<?php

namespace TDGLBundle\Listener;

use NetBS\CoreBundle\Event\ExtendMainMenuEvent;
use NetBS\SecureBundle\Entity\User;
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
        /** @var User $user */
        $menu   = $event->getMenu();
        $links  = $menu->getCategory('app')->getLinks();
        $user   = $this->storage->getToken()->getUser();

        foreach($links as $link)
            if($link->getKey() === 'fichier')
                $link->addSubLink('Ajouter un membre', 'sauvabelin.membre.add_membre');

        $adminCategory  = $menu->getCategory('secure.admin');

        if($user->hasRole("ROLE_SG")) {
            $adminCategory->getLink('netbs.secure.admin.users')
                ->addSubLink('Derniers comptes', 'sauvabelin.user.latest_created');
        }

        if($user->hasRole("ROLE_ADMIN")) {
            $mailsMenu = $adminCategory->addSubMenu('bs.mails', 'Mails BS', 'fas fa-envelope');
            $mailsMenu->addSubLink("Mailing listes", "sauvabelin.mailing_lists.lists_mailing_lists");
        }

        if($user->hasRole('ROLE_TRESORIER')) {
            $menu->getCategory('ovesco.facturation')->getLink('facturation.autre')
                ->addSubLink('Cotisations', 'netbs.core.automatic_list.view_list', ['alias' => 'sauvabelin.cotisations']);
        }
    }
}
