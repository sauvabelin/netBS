<?php

namespace SauvabelinBundle\Listener;

use SauvabelinBundle\Entity\BSUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ForceNewPasswordListener
{
    private $router;

    private $storage;

    private $session;

    public function __construct(TokenStorage $storage, Router $router, Session $session)
    {
        $this->storage  = $storage;
        $this->router   = $router;
        $this->session  = $session;
    }

    public function verifyUser(GetResponseEvent $event) {

        /** @var BSUser $user */
        if(!$this->storage->getToken())
            return;

        $user   = $this->storage->getToken()->getUser();

        if(!$user instanceof BSUser)
            return;

        if($user->hasRole('ROLE_ADMIN'))
            return;

        if($user->isNewPasswordRequired()
            && $event->getRequest()->getRequestUri() !== $this->router->generate('netbs.secure.user.account_page')
            && $event->getRequestType() === 1) {

            if($user->hasRole("ROLE_ADMIN")) {

                $this->session->getFlashBag()->add('warning',
                    "T'es admin mais pense à changer de mot de passe!");
                return;
            }

            $this->session->getFlashBag()->add('info', "Avant de pouvoir continuer, veuillez changer de mot de passe.");
            $event->setResponse(new RedirectResponse($this->router->generate('netbs.secure.user.account_page')));
        }
    }
}
