<?php

namespace TenteBundle\ListModel;

use NetBS\CoreBundle\ListModel\Action\ModalAction;
use NetBS\CoreBundle\ListModel\Action\RemoveAction;
use NetBS\CoreBundle\ListModel\Column\ActionColumn;
use NetBS\CoreBundle\ListModel\Column\HelperColumn;
use NetBS\CoreBundle\Utils\Traits\EntityManagerTrait;
use NetBS\CoreBundle\Utils\Traits\RouterTrait;
use NetBS\ListBundle\Column\DateTimeColumn;
use NetBS\ListBundle\Column\SimpleColumn;
use NetBS\ListBundle\Model\BaseListModel;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use TenteBundle\Entity\FeuilleEtat;

class TenteFeuillesEtatList extends BaseListModel
{
    use EntityManagerTrait, RouterTrait;

    /**
     * Retrieves all elements managed by this list
     * @return array
     */
    protected function buildItemsList()
    {
        return $this->getParameter('tente')->getFeuillesEtat();
    }

    /**
     * Returns the class of items managed by this list
     * @return string
     */
    public function getManagedItemsClass()
    {
        return FeuilleEtat::class;
    }

    /**
     * Returns this list's alias
     * @return string
     */
    public function getAlias()
    {
        return 'tente.tente_feuilles_etat';
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(\NetBS\ListBundle\Model\ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn("Rédacteur", 'user', HelperColumn::class)
            ->addColumn('Unité', 'groupe', HelperColumn::class)
            ->addColumn('Statut', function(FeuilleEtat $feuilleEtat) {
                if ($feuilleEtat->getStatut() === FeuilleEtat::STATUS_NO_OK)
                    return "<span class='label label-danger'>Problème</span>";
                return 'Ok';
            }, SimpleColumn::class)
            ->addColumn('Début activité', 'debut', DateTimeColumn::class)
            ->addColumn('Fin activité', 'fin', DateTimeColumn::class)
            ->addColumn('Rédigée le', 'createdAt', DateTimeColumn::class)
            ->addColumn('Actions', null, ActionColumn::class, [
                ActionColumn::ACTIONS_KEY => [
                    ModalAction::class => [
                        ModalAction::ROUTE => function($m) { return $this->router->generate('tente.feuille_etat.view', ['id' => $m->getId()]); },
                        ModalAction::ICON => 'fas fa-book-open'
                    ],
                    RemoveAction::class
                ],
            ]);
    }
}