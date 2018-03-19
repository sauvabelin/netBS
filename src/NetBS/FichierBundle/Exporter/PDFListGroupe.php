<?php

namespace NetBS\FichierBundle\Exporter;

use NetBS\CoreBundle\Exporter\PDFExporter;
use NetBS\FichierBundle\Mapping\BaseAttribution;
use NetBS\FichierBundle\Mapping\BaseGroupe;
use NetBS\FichierBundle\Service\FichierConfig;

class PDFListGroupe extends PDFExporter
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
        return 'pdf.list_groupe';
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
     * @param BaseGroupe[] $groupes
     * @return string
     */
    public function renderView($groupes)
    {
        $groupesData = [];

        foreach($groupes as $groupe) {

            $groupesData[$groupe->getId()]      = [];
            $groupesData[$groupe->getId()][]    = $this->attributionsToMembres($groupe);

            if($groupe->getGroupeType()->getAffichageEffectifs())
                foreach($groupe->getEnfants() as $child)
                    $groupesData[$groupe->getId()][] = $this->attributionsToMembres($child);
        }

        return $this->twig->render('@NetBSFichier/pdf/list_groupe.pdf.twig', array(
            'groupesData' => $groupesData
        ));
    }

    /**
     * @param BaseGroupe $groupe
     * @return array
     */
    protected function attributionsToMembres($groupe) {

        $attributions   = $groupe->getActivesAttributions();

        usort($attributions, BaseAttribution::getSortFunction());

        $result = [];
        foreach($attributions as $attribution)
            $result[] = $attribution->getMembre();

        return [
            'groupe'    => $groupe,
            'membres'   => $result
        ];
    }
}