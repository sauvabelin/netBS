<?php

namespace Ovesco\FacturationBundle\Listener;

use NetBS\CoreBundle\Event\NetbsRendererToolbarEvent;
use NetBS\CoreBundle\ListModel\Renderer\BasicToolbarItem;
use NetBS\CoreBundle\Service\ListBridgeManager;
use NetBS\FichierBundle\Model\AdressableInterface;
use Ovesco\FacturationBundle\Entity\Creance;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class NetbsToolbarListener
{
    /**
     * @var ListBridgeManager
     */
    private $bridgeManager;

    /**
     * @var TokenStorage
     */
    private $storage;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(ListBridgeManager $bridgeManager, \Twig_Environment $twig, TokenStorage $storage)
    {
        $this->bridgeManager        = $bridgeManager;
        $this->twig                 = $twig;
        $this->storage              = $storage;
    }

    /**
     * @param NetbsRendererToolbarEvent $event
     * @throws \Exception
     */
    public function extend(NetbsRendererToolbarEvent $event) {

        if (!$this->storage->getToken()->getUser()->hasRole('ROLE_TRESORIER')) return;

        $itemClass      = $event->getTable()->getItemClass();

        $addCreances    = $itemClass !== Creance::class && $this->bridgeManager->isValidTransformation($itemClass, AdressableInterface::class);
        $generate       = $itemClass === Creance::class;

        if(!$addCreances && !$generate)
            return;

        $content = $this->twig->render('@OvescoFacturation/renderer/facturation_toolbar.button.twig', [
            'table'         => $event->getTable(),
            'tableId'       => $event->getTableId(),
            'addCreances'   => $addCreances,
            'generate'      => $generate
        ]);

        $event->getToolbar()->addItem(new BasicToolbarItem($content, BasicToolbarItem::RIGHT));
    }
}