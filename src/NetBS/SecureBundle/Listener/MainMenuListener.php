<?php

namespace NetBS\SecureBundle\Listener;

use Doctrine\ORM\EntityManager;
use NetBS\CoreBundle\Event\ExtendMainMenuEvent;
use NetBS\SecureBundle\Mapping\BaseAutorisation;
use NetBS\SecureBundle\Mapping\BaseUser;
use NetBS\SecureBundle\Service\SecureConfig;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class MainMenuListener
{
    /**
     * @var TokenStorage
     */
    protected $token;

    public function __construct(TokenStorage $storage)
    {
        $this->token        = $storage;
    }

    public function onMenuConfigure(ExtendMainMenuEvent $event)
    {
        /** @var BaseUser $user */
        $user       = $this->token->getToken()->getUser();
        $menu       = $event->getMenu();
        $category   = $menu->getCategory('secure.admin');

        //Check if user has autorisations
        $autorisations  = $user->getAutorisations();

        if(count($autorisations) > 0) {
            $authMenu = $category->addSubMenu("secure.autorisations", "Autorisations", "fas fa-cubes");

            /** @var BaseAutorisation $autorisation */
            foreach($autorisations as $autorisation)
                $authMenu->addSubLink($autorisation->getGroupe()->getNom(), "netbs.fichier.groupe.page_groupe", [
                    'id' => $autorisation->getGroupe()->getId()
                ]);
        }

        $subMenu    = $category->addSubMenu('netbs.secure.admin.users', 'Utilisateurs', 'fas fa-key');

        if($user->hasRole("ROLE_ADMIN")) {

            $subMenu
                ->addSubLink('Gestion', 'netbs.secure.user.list_users')
                ->addSubLink('Nouveau', 'netbs.secure.user.add_user')
                ->addSubLink('Autorisations', 'netbs.secure.autorisation.list');
        }
    }
}