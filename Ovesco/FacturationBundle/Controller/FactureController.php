<?php

namespace Ovesco\FacturationBundle\Controller;

use Ovesco\FacturationBundle\Entity\Facture;
use Ovesco\FacturationBundle\Model\FactureConfig;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CreanceController
 * @package Ovesco\FacturationBundle\Controller
 * @Route("/factures")
 */
class FactureController extends AbstractController
{
    /**
     * @Route("/attente-paiement", name="ovesco.facturation.facture.attente_paiement")
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function factureAttentePaiementAction() {
        return $this->search('Factures en attente de paiement', 'no');
    }

    /**
     * @Route("/attente-impression", name="ovesco.facturation.facture.attente_impression")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function factureAttenteImpressionAction() {
        return $this->search('Factures en attente d\'impression', 'yes');
    }

    private function search($title, $printed) {

        $searcher       = $this->get('netbs.core.searcher_manager');
        $instance       = $searcher->bind(Facture::class);
        $params         = [];

        if (!$instance->getForm()->isSubmitted()) {
            $params['title'] = $title;
            $instance->getForm()->get('statut')->submit(Facture::OUVERTE);
            $instance->getForm()->get('isPrinted')->submit($printed);
            $instance->getSearcher()->setForm($instance->getForm());
        }


        return $searcher->render($instance, $params);
    }

    /**
     * @Route("/search", name="ovesco.facturation.search_factures")
     */
    public function searchFactureAction() {

        $searcher       = $this->get('netbs.core.searcher_manager');
        $instance       = $searcher->bind(Facture::class);
        return $searcher->render($instance);
    }

    /**
     * @param Facture $facture
     * @Route("/modal-view/{id}", name="ovesco.facturation.facture_modal")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function factureModalAction(Facture $facture) {
        return $this->render('@OvescoFacturation/facture/facture.modal.twig', [
            'facture' => $facture,
        ]);
    }

    /**
     * @param Facture $facture
     * @Route("/modal-pdf-view/{id}", name="ovesco.facturation.pdf_facture_modal")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function facturePdfModalAction(Facture $facture) {
        return $this->render('@OvescoFacturation/facture/pdf_facture.modal.twig', [
            'facture' => $facture,
        ]);
    }

    /**
     * @param Facture $facture
     * @Route("/facture-pdf-no-print-date/{id}", name="ovesco.facturation.export_pdf_facture_no_date")
     */
    public function facturePdfNoDateExportAction(Facture $facture) {

        $items      = [$facture];
        $exporter   = $this->get('ovesco.facturation.exporter.pdf_factures');
        $config = new FactureConfig(false);
        $exporter->setConfig($config);
        $previewer  = $this->get('netbs.core.previewer_manager')->getPreviewer($exporter->getPreviewer());
        return $previewer->preview($items, $exporter);
    }
}
