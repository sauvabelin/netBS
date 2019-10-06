<?php

namespace TenteBundle\ListModel;

use NetBS\CoreBundle\ListModel\Action\IconAction;
use NetBS\CoreBundle\ListModel\Column\ActionColumn;
use NetBS\CoreBundle\ListModel\Column\XEditableColumn;
use NetBS\CoreBundle\Utils\Traits\EntityManagerTrait;
use NetBS\CoreBundle\Utils\Traits\RouterTrait;
use NetBS\ListBundle\Column\DateTimeColumn;
use NetBS\ListBundle\Column\SimpleColumn;
use NetBS\ListBundle\Model\BaseListModel;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use TenteBundle\Entity\TenteModel;

class TenteModelList extends BaseListModel
{
    use EntityManagerTrait, RouterTrait;

    /**
     * Retrieves all elements managed by this list
     * @return array
     */
    protected function buildItemsList()
    {
        return $this->entityManager->getRepository('TenteBundle:TenteModel')->findAll();
    }

    /**
     * Returns the class of items managed by this list
     * @return string
     */
    public function getManagedItemsClass()
    {
        return TenteModel::class;
    }

    /**
     * Returns this list's alias
     * @return string
     */
    public function getAlias()
    {
        return 'tente.tente_model';
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(\NetBS\ListBundle\Model\ListColumnsConfiguration $configuration)
    {
        $configuration->addColumn('Nom', null, XEditableColumn::class, [
            XEditableColumn::PROPERTY => 'name',
            XEditableColumn::TYPE_CLASS => TextType::class,
        ])->addColumn('Tentes', 'tentes.count', SimpleColumn::class)
            ->addColumn('Ajouté le', 'createdAt', DateTimeColumn::class)
            ->addColumn('Actions', null, ActionColumn::class, [
                ActionColumn::ACTIONS_KEY => [
                    IconAction::class => [
                        IconAction::ROUTE => function($m) { return $this->router->generate('tente.tente_model.view', ['id' => $m->getId()]); },
                        IconAction::ICON => 'fas fa-eye'
                    ]
                ],
            ]);
    }
}