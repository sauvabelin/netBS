<?php

namespace NetBS\CoreBundle\Listener;

use NetBS\CoreBundle\Block\CardBlock;
use NetBS\CoreBundle\Block\Row;
use NetBS\CoreBundle\Event\PreRenderLayoutEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class DashboardListener
{
    protected $stack;

    protected $storage;

    public function __construct(RequestStack $stack, TokenStorage $storage)
    {
        $this->stack    = $stack;
        $this->storage  = $storage;
    }

    public function preRender(PreRenderLayoutEvent $event) {

        if($this->stack->getCurrentRequest()->get('_route') !== "netbs.core.home.dashboard")
            return;

        $row    = $event->getConfigurator()->addRow();

        if(strtolower(php_uname('s')) === 'linux')
            $this->generateSysInfoBlock($row);
    }

    protected function generateSysInfoBlock(Row $row) {

    }
}