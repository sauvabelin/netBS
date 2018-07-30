<?php

namespace SauvabelinBundle\ListModel;

use NetBS\CoreBundle\ListModel\Column\XEditableColumn;
use NetBS\CoreBundle\Utils\Traits\EntityManagerTrait;
use NetBS\ListBundle\Model\BaseListModel;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use SauvabelinBundle\Entity\NewsChannel;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class NewsChannelsList extends BaseListModel
{
    use EntityManagerTrait;

    /**
     * Retrieves all elements managed by this list
     * @return array
     */
    protected function buildItemsList()
    {
        return $this->entityManager->getRepository('SauvabelinBundle:NewsChannel')->findAll();
    }

    /**
     * Returns the class of items managed by this list
     * @return string
     */
    public function getManagedItemsClass()
    {
        return NewsChannel::class;
    }

    /**
     * Returns this list's alias
     * @return string
     */
    public function getAlias()
    {
        return "sauvabelin.news_channels";
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn("Nom", null, XEditableColumn::class, [
                XEditableColumn::PROPERTY   => "nom",
                XEditableColumn::TYPE_CLASS => TextType::class
            ])
        ;
    }
}