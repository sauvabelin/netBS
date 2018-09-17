<?php

namespace NetBS\CoreBundle\Listener;

use Doctrine\ORM\EntityManager;
use NetBS\CoreBundle\Block\CardBlock;
use NetBS\CoreBundle\Block\Row;
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

    public function preRender(PreRenderLayoutEvent $event) {

        if($this->stack->getCurrentRequest()->get('_route') !== "netbs.core.home.dashboard")
            return;

        $row    = $event->getConfigurator()->getRow(0);

        if(strtolower(php_uname('s')) === 'linux')
            $this->generateSysInfoBlock($row);


        $news   = $this->manager->getRepository('NetBSCoreBundle:News')->createQueryBuilder('n')
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $row->addColumn(0, 4, 5, 12)->setBlock(CardBlock::class, array(
            'title'     => 'News',
            'subtitle'  => 'Dernières news publiées',
            'template'  => '@NetBSCore/news/news.block.twig',
            'params'    => ['news' => $news]
        ));
    }

    protected function generateSysInfoBlock(Row $row) {

    }
}