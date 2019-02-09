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
}