<?php

namespace NetBS\FichierBundle\Exporter;

use NetBS\CoreBundle\Exporter\PDFPreviewer;
use NetBS\CoreBundle\Model\ConfigurableExporterInterface;
use NetBS\CoreBundle\Model\ExporterInterface;
use NetBS\CoreBundle\Utils\Traits\ConfigurableExporterTrait;
use NetBS\FichierBundle\Exporter\Config\EtiquettesConfig;
use NetBS\FichierBundle\Exporter\Model\EtiquettesBuilder;
use NetBS\FichierBundle\Form\Export\EtiquettesType;
use NetBS\FichierBundle\Model\AdressableInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PDFEtiquettes implements ExporterInterface, ConfigurableExporterInterface
{
    use ConfigurableExporterTrait;

    /**
     * Returns an alias representing this exporter
     * @return string
     */
    public function getAlias()
    {
        return 'pdf.etiquettes';
    }

    /**
     * Returns the exported item's class
     * @return string
     */
    public function getExportableClass()
    {
        return AdressableInterface::class;
    }

    /**
     * Returns a displayable name of this exporter
     * @return string
     */
    public function getName()
    {
        return "Etiquettes";
    }

    /**
     * @param AdressableInterface[] $adressables
     * @return string
     */
    public function export($adressables)
    {
        /** @var EtiquettesConfig $config */
        $fpdf   = new EtiquettesBuilder($this->configuration);

        $fpdf->AddPage();

        foreach ($adressables as $adressable) {
            
            $adresse = $adressable->getSendableAdresse();

            if ($adresse) {

                $fpdf->addEtiquette(array(
                    $adressable->__toString(),
                    $adresse->getRue(),
                    $adresse->getNpa() . " " . $adresse->getLocalite()
                ));
            }
        }

        return  new StreamedResponse(function() use ($fpdf) {
            $fpdf->Output();
        });
    }

    /**
     * Returns this exported items file extension
     * @return string
     */
    public function getCategory()
    {
        return 'pdf';
    }

    /**
     * Retournes le formulaire de configuration de l'exportation
     * @return string|null
     */
    public function getConfigFormClass()
    {
        return EtiquettesType::class;
    }

    /**
     * Retournes la classe faisant office de configuration pour cette exportation,
     * Si retourne null, aucune configuration n'est chargée et le fichier est directement exporté
     * @return string|null
     */
    public function getConfigClass()
    {
        return EtiquettesConfig::class;
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