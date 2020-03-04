<?php

namespace Ovesco\FacturationBundle\Exporter;

use NetBS\CoreBundle\Exporter\CSVColumns;
use NetBS\CoreBundle\Exporter\CSVExporter;
use NetBS\CoreBundle\Exporter\CSVPreviewer;
use NetBS\CoreBundle\Model\ConfigurableExporterInterface;
use Ovesco\FacturationBundle\Entity\Paiement;
use Ovesco\FacturationBundle\Exporter\Config\CSVPaiementConfig;
use Ovesco\FacturationBundle\Form\Export\CSVPaiementType;

class CSVPaiement extends CSVExporter implements ConfigurableExporterInterface
{
    /**
     * @var CSVPaiementConfig
     */
    protected $exportConfig;

    public function configureColumns(CSVColumns $columns)
    {
        $columns
            ->addColumn('Numero paiement', 'id')
            ->addColumn('Numero facture', 'facture.id')
            ->addColumn('Date de paiement', function(Paiement $paiement) { return $paiement->getDate()->format('d.m.Y');})
            ->addColumn('Montant paiement', 'montant')
        ;

        if ($this->exportConfig->montantFacture)
            $columns->addColumn('Montant facture', 'facture.montant');

        if ($this->exportConfig->creances)
            $columns->addColumn('Créances facture', function (Paiement $paiement) {
                $creances = $paiement->getFacture()->getCreances();
                $txt = '';
                foreach($creances as $creance) $txt .= $creance->getTitre() . "\n";
                return $txt;
            });

        if ($this->exportConfig->compte)
            $columns->addColumn('Compte', 'compte.ccp');
    }

    /**
     * Returns an alias representing this exporter
     * @return string
     */
    public function getAlias()
    {
        return 'facturation.csv.paiements';
    }

    /**
     * Returns the exported item's class
     * @return string
     */
    public function getExportableClass()
    {
        return Paiement::class;
    }

    /**
     * Returns a displayable name of this exporter
     * @return string
     */
    public function getName()
    {
        return 'Exportation CSV';
    }

    /**
     * Returns the form used to configure the export
     * @return string
     */
    public function getConfigFormClass()
    {
        return CSVPaiementType::class;
    }

    /**
     * Returns the configuration object class
     * @return string
     */
    public function getBasicConfig()
    {
        return new CSVPaiementConfig();
    }

    /**
     * Sets the configuration for this export
     * @param $config
     */
    public function setConfig($config)
    {
        $this->exportConfig = $config;
    }

    /**
     * If the rendered file can be previewed, return the used
     * previewer class
     * @return string
     */
    public function getPreviewer()
    {
        return CSVPreviewer::class;
    }
}
