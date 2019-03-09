<?php

namespace Ovesco\FacturationBundle\Controller;

use Ovesco\FacturationBundle\Entity\Facture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CreanceController
 * @package Ovesco\FacturationBundle\Controller
 * @Route("/factures")
 */
class FactureController extends Controller
{
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
     * @Route("/attente-paiement", name="ovesco.facturation.facture.attente_paiement")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function factureAttentePaiementAction() {
        return $this->render('@NetBSCore/generic/list.generic.twig', [
            'header' => "Factures impayées",
            'subHeader' => "Factures en attente de paiement",
            "cardTitle" => "Liste des factures",
            "list" => "ovesco.facturation.factures_attente_paiement"
        ]);
    }

    /**
     * @Route("/attente-impression", name="ovesco.facturation.facture.attente_impression")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function factureAttenteImpressionAction() {
        return $this->render('@NetBSCore/generic/list.generic.twig', [
            'header' => "Factures en attente d'impression",
            'subHeader' => "Toutes les factures en attente d'être imprimées et envoyées",
            "cardTitle" => "Liste des factures",
            "list" => "ovesco.facturation.factures_attente_impression"
        ]);
    }
}
