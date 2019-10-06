<?php

namespace TenteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use TenteBundle\Entity\FeuilleEtat;
use TenteBundle\Entity\Tente;
use TenteBundle\Form\TenteType;

/**
 * @package TenteBundle\Controller
 * @Route("/tente")
 */
class TenteController extends Controller
{
    /**
     * @Route("/details/{id}", name="tente.tente.details")
     */
    public function viewAction(Tente $tente) {

        $form = $this->createForm(TenteType::class, $tente);
        $em = $this->get('doctrine.orm.default_entity_manager');
        return $this->render('@Tente/tente/details_tente.html.twig', [
            'tente' => $tente,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/search", name="tente.tente.search")
     */
    public function searchFactureAction() {

        $searcher       = $this->get('netbs.core.searcher_manager');
        $instance       = $searcher->bind(Tente::class);
        return $searcher->render($instance);
    }
}
