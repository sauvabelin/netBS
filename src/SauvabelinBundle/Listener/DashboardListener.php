<?php

namespace SauvabelinBundle\Listener;

use NetBS\CoreBundle\Block\CardBlock;
use NetBS\CoreBundle\Block\ListBlock;
use NetBS\CoreBundle\Event\PreRenderLayoutEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class DashboardListener
{
    protected $stack;

    public function __construct(RequestStack $stack)
    {
        $this->stack    = $stack;
    }

    public function extendsDashboard(PreRenderLayoutEvent $event) {

        $route  = $this->stack->getCurrentRequest()->get('_route');

        if($route !== "netbs.core.home.dashboard")
            return;

        $config = $event->getConfigurator();
        $row    = $config->addRow();

        $row->addColumn(0, 6, 12)->setBlock(CardBlock::class, array(
            'title'     => 'Calendrier BS',
            'subtitle'  => 'Calendriers internes et publiques',
            'template'  => '@Sauvabelin/dashboard/calendrier.block.twig'
        ));

        $row->addColumn(1, 6, 12)->setBlock(ListBlock::class, [
            'alias'     => 'bs.displayable_mailing_lists',
            'title'     => 'Mailing listes',
            'subtitle'  => "Toutes les mailing listes BS"
        ]);
    }
}