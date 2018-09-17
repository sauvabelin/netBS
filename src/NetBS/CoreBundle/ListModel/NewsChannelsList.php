<?php

namespace NetBS\CoreBundle\ListModel;

use NetBS\CoreBundle\Entity\NewsChannel;
use NetBS\CoreBundle\ListModel\Column\XEditableColumn;
use NetBS\CoreBundle\Utils\Traits\EntityManagerTrait;
use NetBS\ListBundle\Column\ClosureColumn;
use NetBS\ListBundle\Model\BaseListModel;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
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
        return $this->entityManager->getRepository('NetBSCoreBundle:NewsChannel')->findAll();
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
        return "netbs.core.news_channels";
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn("Couleur", null, ClosureColumn::class, [
                ClosureColumn::CLOSURE  => function(NewsChannel $channel) {
                    return "<span class='badge' style='background:{$channel->getColor()};color:white'>{$channel->getNom()}</span>";
                }
            ])
            ->addColumn("Nom", null, XEditableColumn::class, [
                XEditableColumn::PROPERTY   => "nom",
                XEditableColumn::TYPE_CLASS => TextType::class
            ])
            ->addColumn("Couleur", null, XEditableColumn::class, [
                XEditableColumn::PROPERTY   => "color",
                XEditableColumn::TYPE_CLASS => TextType::class
            ])
        ;
    }
}