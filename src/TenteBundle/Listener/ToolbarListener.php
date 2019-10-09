<?php

namespace TenteBundle\Listener;

use NetBS\CoreBundle\Event\NetbsRendererToolbarEvent;
use NetBS\CoreBundle\ListModel\Renderer\BasicToolbarItem;
use TenteBundle\Entity\FeuilleEtat;

class ToolbarListener
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param NetbsRendererToolbarEvent $event
     */
    public function extend(NetbsRendererToolbarEvent $event) {

        $itemClass  = $event->getTable()->getItemClass();
        if (!($itemClass === FeuilleEtat::class)) return;
        $content = $this->twig->render('@Tente/feuilles/toolbar_extend.twig', [
            'tableId' => $event->getTableId(),
        ]);
        $event->getToolbar()->addItem(new BasicToolbarItem($content));
    }
}
