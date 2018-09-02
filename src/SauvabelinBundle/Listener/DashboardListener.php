<?php

namespace SauvabelinBundle\Listener;

use Doctrine\ORM\EntityManager;
use NetBS\CoreBundle\Block\CardBlock;
use NetBS\CoreBundle\Block\ListBlock;
use NetBS\CoreBundle\Event\PreRenderLayoutEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class DashboardListener
{
    protected $stack;

    protected $manager;

    public function __construct(RequestStack $stack, EntityManager $manager)
    {
        $this->stack    = $stack;
        $this->manager  = $manager;
    }

    /**
     * @param PreRenderLayoutEvent $event
     * @throws \Exception
     */
    public function extendsDashboard(PreRenderLayoutEvent $event) {

        $route  = $this->stack->getCurrentRequest()->get('_route');

        if($route !== "netbs.core.home.dashboard")
            return;

        $config = $event->getConfigurator();
        $row    = $config->addRow();
        $news   = $this->manager->getRepository('SauvabelinBundle:News')->createQueryBuilder('n')
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $row->addColumn(0, 4, 6, 12)->setBlock(CardBlock::class, array(
            'title'     => 'News',
            'subtitle'  => 'Dernières news publiées',
            'template'  => '@Sauvabelin/block/news.block.twig',
            'params'    => ['news' => $news]
        ));

        $row->addColumn(1, 4, 6, 12)->setBlock(CardBlock::class, array(
            'title'     => 'Calendrier BS',
            'subtitle'  => 'Calendriers internes et publiques',
            'template'  => '@Sauvabelin/block/calendrier.block.twig'
        ));

        $row->addColumn(2, 4, 6, 12)->setBlock(ListBlock::class, [
            'alias'     => 'bs.displayable_mailing_lists',
            'title'     => 'Mailing listes',
            'subtitle'  => "Toutes les mailing listes BS"
        ]);
    }
}