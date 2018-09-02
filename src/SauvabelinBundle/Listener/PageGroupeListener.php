<?php

namespace SauvabelinBundle\Listener;

use NetBS\CoreBundle\Block\TemplateBlock;
use NetBS\CoreBundle\Event\PreRenderLayoutEvent;
use NetBS\CoreBundle\Service\ParameterManager;
use NetBS\FichierBundle\Mapping\BaseGroupe;
use Symfony\Component\HttpFoundation\RequestStack;

class PageGroupeListener
{
    protected $twig;

    protected $stack;

    protected $params;

    public function __construct(RequestStack $stack, ParameterManager $params, \Twig_Environment $twig)
    {
        $this->twig     = $twig;
        $this->stack    = $stack;
        $this->params   = $params;
    }

    /**
     * @param PreRenderLayoutEvent $event
     * @throws \Exception
     */
    public function extendsPageGroupe(PreRenderLayoutEvent $event) {

        $route  = $this->stack->getCurrentRequest()->get('_route');

        if($route !== "netbs.fichier.groupe.page_groupe")
            return;

        /** @var BaseGroupe $groupe */
        $groupe = $event->getParameters()['item'];

        if($groupe->getGroupeType()->getGroupeCategorie()->getId() !== intval($this->params->getValue('bs', 'groupe_categorie.unite_id')))
            return;

        $config = $event->getConfigurator();
        $row    = $config->getRow(0)->getColumns()[0]->addRow();

        $row->addColumn(0, 12)->setBlock(TemplateBlock::class, [
            'template'  => '@Sauvabelin/block/liste_unite.block.twig',
            'params'    => [
                'groupe'    => $groupe
            ]
        ]);
    }
}