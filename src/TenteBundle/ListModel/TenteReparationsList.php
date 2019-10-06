<?php

namespace TenteBundle\ListModel;

use NetBS\CoreBundle\Form\Type\DatepickerType;
use NetBS\CoreBundle\ListModel\Column\XEditableColumn;
use NetBS\CoreBundle\Utils\Traits\EntityManagerTrait;
use NetBS\CoreBundle\Utils\Traits\RouterTrait;
use NetBS\ListBundle\Model\BaseListModel;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use TenteBundle\Entity\Reparation;

class TenteReparationsList extends BaseListModel
{
    use EntityManagerTrait, RouterTrait;

    /**
     * Retrieves all elements managed by this list
     * @return array
     */
    protected function buildItemsList()
    {
        return $this->entityManager->getRepository('TenteBundle:Reparation')
            ->findBy(['tente' => $this->getParameter('tenteId')]);
    }

    /**
     * Returns the class of items managed by this list
     * @return string
     */
    public function getManagedItemsClass()
    {
        return Reparation::class;
    }

    /**
     * Returns this list's alias
     * @return string
     */
    public function getAlias()
    {
        return 'tente.tente_reparations';
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(\NetBS\ListBundle\Model\ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn('Statut', null, XEditableColumn::class, [
                XEditableColumn::PROPERTY => 'status',
                XEditableColumn::TYPE_CLASS => ChoiceType::class,
                XEditableColumn::PARAMS => [
                    'choices' => array_flip(Reparation::getStatusChoices()),
                ],
            ])
            ->addColumn('Envoyée le', null, XEditableColumn::class, [
                XEditableColumn::PROPERTY => 'envoyee',
                XEditableColumn::TYPE_CLASS => DatepickerType::class,
            ])
            ->addColumn('Retour le', null, XEditableColumn::class, [
                XEditableColumn::PROPERTY => 'recue',
                XEditableColumn::TYPE_CLASS => DatepickerType::class,
            ])
            ->addColumn('Remarques', null, XEditableColumn::class, [
                XEditableColumn::PROPERTY => 'remarques',
                XEditableColumn::TYPE_CLASS => TextareaType::class,
            ]);
    }
}