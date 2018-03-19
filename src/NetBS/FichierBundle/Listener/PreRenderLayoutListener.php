<?php

namespace NetBS\FichierBundle\Listener;

use Doctrine\ORM\EntityManager;
use NetBS\CoreBundle\Block\CardBlock;
use NetBS\CoreBundle\Event\PreRenderLayoutEvent;
use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\SecureBundle\Mapping\BaseUser;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class PreRenderLayoutListener
{
    protected $token;

    protected $stack;

    protected $manager;

    public function __construct(TokenStorage $storage, RequestStack $stack, EntityManager $manager)
    {
        $this->token    = $storage;
        $this->stack    = $stack;
        $this->manager  = $manager;
    }

    public function preRender(PreRenderLayoutEvent $event) {

    }
}