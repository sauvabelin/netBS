<?php

namespace NetBS\FichierBundle\Exporter;

use NetBS\CoreBundle\Exporter\CSVColumns;
use NetBS\CoreBundle\Exporter\CSVExporter;
use NetBS\CoreBundle\Utils\StrUtil;
use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\FichierBundle\Mapping\Personne;
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
            ->addColumn('SEXE', function (BaseMembre $membre) {
                return $membre->getSexe() === Personne::FEMME ? '2' : '1';
            })
            ->addColumn('NOM', function(BaseMembre $membre) {
                return StrUtil::removeAccents($membre->getFamille()->getNom());
            })
            ->addColumn('PRENOM', function (BaseMembre $membre) {
                return StrUtil::removeAccents($membre->getPrenom());
            })
            ->addColumn('DAT_NAISSANCE', function(BaseMembre $membre) {
                return $membre->getNaissance()->format('d.m.Y');
            })
            ->addColumn('RUE', function(BaseMembre $membre) {
                if($adresse = $membre->getSendableAdresse())
                    return StrUtil::removeAccents($adresse->getRue());
            })
            ->addColumn('NPA', function(BaseMembre $membre) {
                if($adresse = $membre->getSendableAdresse())
                    return $adresse->getNpa();
            })
            ->addColumn('LOCALITE', function(BaseMembre $membre) {
                if($adresse = $membre->getSendableAdresse())
                    return StrUtil::removeAccents($adresse->getLocalite());
            })
            ->addColumn('PAYS', function(BaseMembre $membre) {
                return 'CH';
            })
            ->addColumn('NATIONALITE', function(BaseMembre $membre) {
                return 'CH';
            })
            ->addColumn('1ERE_LANGUE', function (BaseMembre $membre) {
                return 'F';
            })
            ->addColumn('CLASSE/GROUPE', function(BaseMembre $membre) {
                if($attr = $membre->getActiveAttribution())
                    return StrUtil::removeAccents($attr->getGroupe()->getNom());
            })
        ;
    }
}
