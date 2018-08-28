<?php

namespace NetBS\FichierBundle\ListModel;

use NetBS\CoreBundle\ListModel\AbstractDynamicListModel;
use NetBS\CoreBundle\ListModel\Column\HelperColumn;
use NetBS\CoreBundle\ListModel\Column\RemoveFromDynamicColumn;
use NetBS\FichierBundle\Utils\Traits\FichierConfigTrait;
use NetBS\ListBundle\Column\DateTimeColumn;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use NetBS\CoreBundle\Utils\Traits\RouterTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ConfiguratorTrait;

class DynamicMembreList extends AbstractDynamicListModel
{
    use RouterTrait, ConfiguratorTrait, FichierConfigTrait;

    public function getManagedName()
    {
        return "Membres";
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn('Nom', null, HelperColumn::class)
            ->addColumn('Date de naissance', 'naissance', DateTimeColumn::class)
            ->addColumn('Retirer', null, RemoveFromDynamicColumn::class, [
                'listId'    => $this->getParameter(self::LIST_ID)
            ])
        ;
    }

    /**
     * Returns the class of items managed by this list
     * @return string
     */
    public function getManagedItemsClass()
    {
        return $this->getFichierConfig()->getMembreClass();
    }

    /**
     * Returns this list's alias
     * @return string
     */
    public function getAlias()
    {
        return 'netbs.fichier.dynamic.membres';
    }
}