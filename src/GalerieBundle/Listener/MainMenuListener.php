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
    }
}
