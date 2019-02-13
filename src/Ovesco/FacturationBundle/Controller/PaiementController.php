<?php

namespace Ovesco\FacturationBundle\Controller;

use NetBS\CoreBundle\Utils\Modal;
use Ovesco\FacturationBundle\Entity\Facture;
use Ovesco\FacturationBundle\Entity\Paiement;
use Ovesco\FacturationBundle\Form\PaiementType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CompteController
 * @package Ovesco\FacturationBundle\Controller
 * @Route("/paiement/{id}")
 */
class PaiementController extends Controller
{
    /**
     * @param Facture $facture
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/modal-add", name="ovesco.facturation.paiement.modal_add")
     */
    public function modalAddAction(Facture $facture, Request $request) {

        $paiement = new Paiement();
        $form = $this->createForm(PaiementType::class, $paiement);

        $form->handleRequest($request);
        if($form->isValid() && $form->isSubmitted()) {

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($paiement);
            $facture->addPaiement($paiement);
            $em->flush();
            $this->addFlash('success', 'Paiement ajouté avec succès');
            return Modal::refresh();
        }

        return $this->render('@NetBSFichier/generic/add_generic.modal.twig', array(
            'form'  => $form->createView()
        ), Modal::renderModal($form));

    }
}