<?php

namespace NetBS\FichierBundle\Listener;

use NetBS\CoreBundle\Event\NetbsRendererToolbarEvent;
use NetBS\CoreBundle\ListModel\Renderer\BasicToolbarItem;
use NetBS\FichierBundle\ListModel\DynamicMembreList;
use NetBS\FichierBundle\Mapping\BaseMembre;

class NetBSToolbarListener
{
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

        if(!$event->getTable()->getModel() instanceof DynamicMembreList)
            return;

        $content    = $this->twig->render('@NetBSFichier/renderer/remove_from_dynamics.button.twig', [
            'event'     => $event,
            'listId'    => $event->getTable()->getModel()->getParameter('listId')
        ]);

        $event->getToolbar()->addItem(new BasicToolbarItem($content, 'right'));
    }

}