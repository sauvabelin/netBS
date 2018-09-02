<?php

namespace SauvabelinBundle\Exporter;

use NetBS\CoreBundle\Exporter\PDFExporter;
use NetBS\FichierBundle\Mapping\BaseAttribution;
use NetBS\FichierBundle\Mapping\BaseGroupe;
use NetBS\FichierBundle\Service\FichierConfig;

class PDFUnite extends PDFExporter
{
    private $config;

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
        return "pdf.unite";
    }

    /**
     * Returns the exported item's class
     * @return string
     */
    public function getExportableClass()
    {
        return $this->config->getGroupeClass();
    }

    /**
     * Returns a displayable name of this exporter
     * @return string
     */
    public function getName()
    {
        return "Liste d'unité";
    }

    /**
     * @param BaseGroupe[] $unites
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderView($unites)
    {
        $unite      = $unites[0];
        $sections   = [];

        /** @var BaseGroupe $groupe */
        foreach(array_merge([$unite], $unite->getEnfants()->toArray()) as $groupe)
            if($groupe->getValidity() === BaseGroupe::OUVERT)
                $sections[] = ['groupe' => $groupe, 'membres' => $this->orderSection($groupe)];

        $total      = 0;
        foreach($sections as $section)
            $total += count($section['membres']);

        return $this->twig->render('@Sauvabelin/pdf/liste_unite.pdf.twig', array(
            'sections'  => $sections,
            'groupe'    => $unite,
            'total'     => $total
        ));
    }

    private function orderSection(BaseGroupe $groupe) {

        $attributions   = $groupe->getActivesAttributions();
        usort($attributions, BaseAttribution::getSortFunction());

        return array_map(function(BaseAttribution $attribution) {
            return $attribution->getMembre();
        }, $attributions);
    }
}