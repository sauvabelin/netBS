<?php

namespace NetBS\FichierBundle\Exporter;

use NetBS\CoreBundle\Exporter\CSVColumns;
use NetBS\CoreBundle\Exporter\CSVExporter;
use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\FichierBundle\Service\FichierConfig;

class CSVRega extends CSVExporter
{
    protected $config;

    public function __construct(FichierConfig $config)
    {
        $this->config   = $config;
    }

    /**
     * Returns an alias representing this exporter
     * @return string
     */
    public function getAlias()
    {
        return 'csv.rega';
    }

    /**
     * Returns the exported item's class
     * @return string
     */
    public function getExportableClass()
    {
        return $this->config->getMembreClass();
    }

    /**
     * Returns a displayable name of this exporter
     * @return string
     */
    public function getName()
    {
        return "Liste rega";
    }

    /**
     * @param CSVColumns $columns
     */
    public function configureColumns(CSVColumns $columns)
    {
        $columns
            ->addColumn('NO_PERS_BDNJS', function(BaseMembre $membre) {
                return null;
            })
            ->addColumn('NOM', 'famille.nom')
            ->addColumn('PRENOM', 'prenom')
            ->addColumn('DAT_NAISSANCE', function(BaseMembre $membre) {
                return $membre->getNaissance()->format('d.m.Y');
            })
            ->addColumn('RUE', function(BaseMembre $membre) {
                if($adresse = $membre->getSendableAdresse())
                    return $adresse->getRue();
            })
            ->addColumn('NPA', function(BaseMembre $membre) {
                if($adresse = $membre->getSendableAdresse())
                    return $adresse->getNpa();
            })
            ->addColumn('LOCALITE', function(BaseMembre $membre) {
                if($adresse = $membre->getSendableAdresse())
                    return $adresse->getLocalite();
            })
            ->addColumn('RUE', function(BaseMembre $membre) {
                return 'CH';
            })
            ->addColumn('1ERE LANGUE', function (BaseMembre $membre) {
                return 'F';
            })
            ->addColumn('CLASSE/GROUPE', function(BaseMembre $membre) {
                if($attr = $membre->getActiveAttribution())
                    return $attr->getGroupe()->getNom();
            })
        ;
    }
}