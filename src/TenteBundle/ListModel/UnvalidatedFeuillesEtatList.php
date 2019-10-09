<?php

namespace TenteBundle\ListModel;

use NetBS\CoreBundle\Form\Type\SwitchType;
use NetBS\CoreBundle\ListModel\Action\ModalAction;
use NetBS\CoreBundle\ListModel\Action\RemoveAction;
use NetBS\CoreBundle\ListModel\Column\ActionColumn;
use NetBS\CoreBundle\ListModel\Column\HelperColumn;
use NetBS\CoreBundle\ListModel\Column\XEditableColumn;
use NetBS\CoreBundle\Utils\Traits\EntityManagerTrait;
use NetBS\CoreBundle\Utils\Traits\RouterTrait;
use NetBS\ListBundle\Column\DateTimeColumn;
use NetBS\ListBundle\Column\SimpleColumn;
use NetBS\ListBundle\Model\BaseListModel;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use TenteBundle\Entity\FeuilleEtat;
use TenteBundle\Entity\Tente;

class UnvalidatedFeuillesEtatList extends BaseListModel
{
    use EntityManagerTrait, RouterTrait;

    /**
     * Retrieves all elements managed by this list
     * @return array
     */
    protected function buildItemsList()
    {
        return $this->entityManager->getRepository('TenteBundle:FeuilleEtat')
            ->createQueryBuilder('f')
            ->where('f.validated = FALSE')
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
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
        return 'tente.unvalidated_feuilles_etat';
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(\NetBS\ListBundle\Model\ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn('Statut', function(FeuilleEtat $feuilleEtat) {
                if ($feuilleEtat->getStatut() === FeuilleEtat::STATUS_NO_OK)
                    return "<span class='badge badge-danger'>Problème</span>";
                return "<span class='badge badge-success'>Ok</span>";
            }, SimpleColumn::class)
            ->addColumn("Rédacteur", 'user', HelperColumn::class)
            ->addColumn('Lue', null, XEditableColumn::class, [
                XEditableColumn::TYPE_CLASS => SwitchType::class,
                XEditableColumn::PROPERTY => 'validated'
            ])
            ->addColumn('Unité', 'groupe', HelperColumn::class)
            ->addColumn('N° Tente', 'tente.numero', SimpleColumn::class)
            ->addColumn('Statut tente', 'tente', XEditableColumn::class, [
                XEditableColumn::TYPE_CLASS => ChoiceType::class,
                XEditableColumn::PROPERTY => 'status',
                XEditableColumn::PARAMS => ['choices' => array_flip(Tente::getStatutChoices())]
            ])
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