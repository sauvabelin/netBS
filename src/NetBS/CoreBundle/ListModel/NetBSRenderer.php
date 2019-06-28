<?php

namespace NetBS\CoreBundle\ListModel;

use NetBS\CoreBundle\Event\NetbsRendererToolbarEvent;
use NetBS\CoreBundle\ListModel\Renderer\Toolbar;
use NetBS\CoreBundle\Service\LoaderManager;
use NetBS\ListBundle\Model\RendererInterface;
use NetBS\ListBundle\Model\SnapshotTable;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NetBSRenderer implements RendererInterface
{
    protected $engine;

    protected $dispatcher;

    protected $loaders;

    public function __construct(\Twig_Environment $engine, EventDispatcherInterface $dispatcher, LoaderManager $manager)
    {
        $this->engine           = $engine;
        $this->dispatcher       = $dispatcher;
        $this->loaders          = $manager;
    }

    /**
     * Returns this renderer's name
     * @return string
     */
    public function getName()
    {
        return 'netbs';
    }

    /**
     * Renders the given prototype table
     * @param SnapshotTable $table
     * @return string
     * @throws \Exception
     */
    public function render(SnapshotTable $table, $params = [])
    {
        $idMapper = function($item) {
            return $item->getId();
        };

        $itemClass = $table->getModel()->getManagedItemsClass();
        if ($this->loaders->hasLoader($itemClass)) {
            $idMapper = function($item) use ($itemClass) {
                return $this->loaders->getLoader($itemClass)->toId($item);
            };
        }
        $toolbar    = new Toolbar();
        $tableId    = uniqid("__dt_");
        $event      = new NetbsRendererToolbarEvent($toolbar, $table, $tableId);

        $this->dispatcher->dispatch(NetbsRendererToolbarEvent::NAME, $event);

        return $this->engine->render('@NetBSCore/renderer/netbs.renderer.twig', array(
            'table'     => $table,
            'tableId'   => $tableId,
            'toolbar'   => $toolbar,
            'params'    => $params,
            'idMapper'  => $idMapper,
        ));
    }
}
