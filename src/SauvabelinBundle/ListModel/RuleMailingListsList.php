<?php

namespace SauvabelinBundle\ListModel;

use NetBS\CoreBundle\ListModel\Action\RemoveAction;
use NetBS\CoreBundle\ListModel\Column\ActionColumn;
use NetBS\CoreBundle\ListModel\Column\XEditableColumn;
use NetBS\CoreBundle\Utils\Traits\EntityManagerTrait;
use NetBS\CoreBundle\Utils\Traits\RouterTrait;
use NetBS\ListBundle\Model\BaseListModel;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use SauvabelinBundle\Entity\RuleMailingList;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RuleMailingListsList extends BaseListModel
{
    use EntityManagerTrait, RouterTrait;

    /**
     * Retrieves all elements managed by this list
     * @return array
     */
    protected function buildItemsList()
    {
        return $this->entityManager->getRepository('SauvabelinBundle:RuleMailingList')->findAll();
    }

    /**
     * Returns the class of items managed by this list
     * @return string
     */
    public function getManagedItemsClass()
    {
        return RuleMailingList::class;
    }

    /**
     * Returns this list's alias
     * @return string
     */
    public function getAlias()
    {
        return 'bs.rule_mailing_lists';
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn('adresse de redirection', null, XEditableColumn::class, [
                XEditableColumn::PROPERTY   => 'fromAdresse',
                XEditableColumn::TYPE_CLASS => TextType::class
            ])
            ->addColumn('Description', null, XEditableColumn::class, [
                XEditableColumn::PROPERTY   => 'description',
                XEditableColumn::TYPE_CLASS => TextareaType::class
            ])
            ->addColumn('Règle EL', null, XEditableColumn::class, [
                XEditableColumn::PROPERTY   => 'elRule',
                XEditableColumn::TYPE_CLASS => TextareaType::class
            ])
            ->addColumn('Actions', null, ActionColumn::class, [
                ActionColumn::ACTIONS_KEY   => [
                    RemoveAction::class
                ]
            ]);
    }
}