<?php

namespace SauvabelinBundle\ListModel;

use NetBS\CoreBundle\ListModel\Action\RemoveAction;
use NetBS\CoreBundle\ListModel\Column\ActionColumn;
use NetBS\CoreBundle\ListModel\Column\XEditableColumn;
use NetBS\CoreBundle\Utils\Traits\EntityManagerTrait;
use NetBS\CoreBundle\Utils\Traits\RouterTrait;
use NetBS\ListBundle\Model\BaseListModel;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use SauvabelinBundle\Entity\RedirectMailingList;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RedirectMailingListsList extends BaseListModel
{
    use EntityManagerTrait, RouterTrait;

    /**
     * Retrieves all elements managed by this list
     * @return array
     */
    protected function buildItemsList()
    {
        return $this->entityManager->getRepository('SauvabelinBundle:RedirectMailingList')->findAll();
    }

    /**
     * Returns the class of items managed by this list
     * @return string
     */
    public function getManagedItemsClass()
    {
        return RedirectMailingList::class;
    }

    /**
     * Returns this list's alias
     * @return string
     */
    public function getAlias()
    {
        return 'bs.redirect_mailing_lists';
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn("Adresse d'origine", null, XEditableColumn::class, [
                XEditableColumn::PROPERTY   => 'fromAdresse',
                XEditableColumn::TYPE_CLASS => TextType::class
            ])
            ->addColumn("Adresses d'arrivée (séparées par une ',')", null, XEditableColumn::class, [
                XEditableColumn::PROPERTY   => 'toAdresses',
                XEditableColumn::TYPE_CLASS => TextareaType::class
            ])
            ->addColumn('Description', null, XEditableColumn::class, [
                XEditableColumn::PROPERTY   => 'description',
                XEditableColumn::TYPE_CLASS => TextareaType::class
            ])
            ->addColumn('Actions', null, ActionColumn::class, [
                ActionColumn::ACTIONS_KEY   => [
                    new RemoveAction($this->router)
                ]
            ]);
    }
}