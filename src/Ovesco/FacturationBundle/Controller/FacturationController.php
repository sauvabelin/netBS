<?php

namespace Ovesco\FacturationBundle\Controller;

use Ovesco\FacturationBundle\Entity\Facture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class FacturationController extends Controller
{
    /**
     * @Route("/dashboard", name="ovesco.facturation.dashboard")
     */
    public function dashboardAction() {

        $attentePaiement = $this->get('ovesco.facturation.list.facture_attente_paiement')->getElements();
        $attenteImpression = $this->get('ovesco.facturation.list.facture_attente_impression')->getElements();
        return $this->render('@OvescoFacturation/dashboard.html.twig', [
            'attentePaiement' => count($attentePaiement),
            'amountPaiement' => $this->amountThunes($attentePaiement),
            'attenteImpression' => count($attenteImpression),
            'amountImpression' => $this->amountThunes($attenteImpression),
        ]);
    }

    /**
     * @param Facture[] $factures
     * @return mixed
     */
    private function amountThunes($factures) {
        return array_reduce($factures, function($carry, Facture $facture) {
            return $carry + $facture->getMontantEncoreDu();
        }, 0);
    }
}