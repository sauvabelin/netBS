<?php

namespace NetBS\FichierBundle\Exporter;

use NetBS\CoreBundle\Exporter\PDFExporter;
use NetBS\CoreBundle\Exporter\PDFPreviewer;
use NetBS\CoreBundle\Model\ConfigurableExporterInterface;
use NetBS\CoreBundle\Utils\Traits\ConfigurableExporterTrait;
use NetBS\FichierBundle\Exporter\Config\PDFListMembresConfig;
use NetBS\FichierBundle\Form\Export\PDFListMembresType;
use NetBS\FichierBundle\Service\FichierConfig;

class PDFListMembres extends PDFExporter implements ConfigurableExporterInterface
{
    use ConfigurableExporterTrait;

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
        return 'pdf.list_membres';
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
        return "Liste simple";
    }

    public function renderView($items)
    {
        return $this->twig->render('@NetBSFichier/pdf/list_membres.pdf.twig', array(
            'items'     => $items,
            'config'    => $this->configuration
        ));
    }

    /**
     * Returns the form used to configure the export
     * @return string
     */
    public function getConfigFormClass()
    {
        return PDFListMembresType::class;
    }

    /**
     * Returns the configuration object class
     * @return string
     */
    public function getBasicConfig()
    {
        return new PDFListMembresConfig();
    }

    /**
     * If the rendered file can be previewed, return the used
     * previewer class
     * @return string
     */
    public function getPreviewer()
    {
        return PDFPreviewer::class;
    }
}
