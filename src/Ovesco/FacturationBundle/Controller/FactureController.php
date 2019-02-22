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
}
