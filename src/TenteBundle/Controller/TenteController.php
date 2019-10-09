<?php

namespace TenteBundle\Controller;

use NetBS\CoreBundle\Utils\Modal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use TenteBundle\Entity\FeuilleEtat;
use TenteBundle\Entity\Reparation;
use TenteBundle\Entity\Tente;
use TenteBundle\Form\ReparationType;
use TenteBundle\Form\TenteType;
use TenteBundle\Model\ReparationPartie;

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
     * @Route("/add-reparation/{id}", name="tente.tente.add_reparation_modal")
     * @param Tente $tente
     */
    public function addReparationAction(Request $request, Tente $tente) {

        $reparation = new Reparation();
        $parties = [];
        foreach(explode("\n", $tente->getModel()->getParties()) as $str)
            $parties[] = new ReparationPartie($str);

        $reparation->setParties($parties);
        $form = $this->createForm(ReparationType::class, $reparation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Reparation $reparation */
            $reparation = $form->getData();
            $reparation->setTente($tente);
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($reparation);
            $em->flush();
            $this->addFlash('success', 'Réparation ajoutée');
            return Modal::refresh();
        }

        return $this->render('@Tente/tente/add_reparation.modal.twig', [
            'form' => $form->createView(),
        ], Modal::renderModal($form));
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
