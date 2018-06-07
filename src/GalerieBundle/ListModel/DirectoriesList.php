<?php

namespace GalerieBundle\ListModel;

use GalerieBundle\Entity\Directory;
use NetBS\CoreBundle\Utils\Traits\EntityManagerTrait;
use NetBS\CoreBundle\Utils\Traits\RouterTrait;
use NetBS\ListBundle\Column\SimpleColumn;
use NetBS\ListBundle\Model\BaseListModel;
use NetBS\ListBundle\Model\ListColumnsConfiguration;

class DirectoriesList extends BaseListModel
{
    use RouterTrait, EntityManagerTrait;

    /**
     * Retrieves all elements managed by this list
     * @return array
     */
    protected function buildItemsList()
    {
        return $this->entityManager->getRepository('GalerieBundle:Directory')->findAll();
    }

    /**
     * Returns the class of items managed by this list
     * @return string
     */
    public function getManagedItemsClass()
    {
        return Directory::class;
    }

    /**
     * Returns this list's alias
     * @return string
     */
    public function getAlias()
    {
        return "galerie.directories";
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn("Nom", "name", SimpleColumn::class)
            ->addColumn("Chemin", function(Directory $directory) {
                return "/" . $directory->getsearchPath();
            }, SimpleColumn::class)
        ;
    }
}