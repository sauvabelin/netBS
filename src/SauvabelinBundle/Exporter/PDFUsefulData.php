<?php

namespace SauvabelinBundle\Exporter;

use NetBS\CoreBundle\Exporter\PDFExporter;
use NetBS\CoreBundle\Exporter\PDFPreviewer;
use NetBS\CoreBundle\Model\ConfigurableExporterInterface;
use NetBS\CoreBundle\Service\ParameterManager;
use NetBS\CoreBundle\Utils\Traits\ConfigurableExporterTrait;
use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\FichierBundle\Service\FichierConfig;
use SauvabelinBundle\Form\PDFUsefulDataType;
use SauvabelinBundle\Model\UsefulDataConfig;

class PDFUsefulData extends PDFExporter implements ConfigurableExporterInterface
{
    use ConfigurableExporterTrait;

    private $config;

    private $params;

    public function __construct(FichierConfig $config, ParameterManager $params)
    {
        $this->config   = $config;
        $this->params   = $params;
    }

    /**
     * Returns an alias representing this exporter
     * @return string
     */
    public function getAlias()
    {
        return "pdf.useful_data";
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
        return "Informations diverses";
    }

    /**
     * @param BaseMembre[] $membres
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderView($membres)
    {
        $headers    = ['Membre'];
        if($this->configuration->cravateBleue)
            $headers[] = "Obtention cravate";

        $data = array_map(function(BaseMembre $membre) {

            $row = [$membre->__toString()];

            if($this->configuration->cravateBleue) {

                $cravateId  = $this->params->getValue('bs', 'distinction.cravate_bleue_id');
                $found = false;

                foreach($membre->getObtentionsDistinction() as $obtentionDistinction) {
                    if ($obtentionDistinction->getDistinction()->getId() === intval($cravateId)) {
                        $row[] = $obtentionDistinction->getDate()->format($this->params->getValue('format', 'php_date'));
                        $found = true;
                        break;
                    }
                }

                if(!$found) $row[] = "Pas de cravate";
            }

            return $row;

        }, $membres);

        return $this->twig->render('@Sauvabelin/pdf/useful_data.pdf.twig', array(
            'headers'   => $headers,
            'data'      => $data
        ));
    }

    /**
     * Returns the form used to configure the export
     * @return string
     */
    public function getConfigFormClass()
    {
        return PDFUsefulDataType::class;
    }

    /**
     * Returns the configuration object class
     * @return string
     */
    public function getConfigClass()
    {
        return UsefulDataConfig::class;
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