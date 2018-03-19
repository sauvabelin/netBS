<?php

namespace SauvabelinBundle\Controller;

use NetBS\CoreBundle\Utils\Modal;
use SauvabelinBundle\Entity\RedirectMailingList;
use SauvabelinBundle\Entity\RuleMailingList;
use SauvabelinBundle\Form\RedirectMailingListType;
use SauvabelinBundle\Form\RuleMailingListType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package SauvabelinBundle\Controller
 * @Route("/mailing-lists")
 */
class MailingListsController extends Controller
{
    /**
     * @Route("/all", name="sauvabelin.mailing_lists.lists_mailing_lists")
     */
    public function listMailingListsAction() {

        return $this->render('@Sauvabelin/mail/mailing_lists.html.twig');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/modal/add/rule-list", name="sauvabelin.mailing_lists.modal_add_rule")
     */
    public function modalAddRuleMailingListAction(Request $request) {

        $form   = $this->createForm(RuleMailingListType::class, new RuleMailingList());

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()) {

            $em         = $this->get('doctrine.orm.entity_manager');
            $em->persist($form->getData());
            $em->flush();

            $this->addFlash("success", "Mailing list ajoutée!");
            return Modal::refresh();
        }

        return $this->render('@NetBSFichier/generic/add_generic.modal.twig', [
            'title' => 'Nouvelle mailing liste Expression Language',
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/modal/add/redirect-list", name="sauvabelin.mailing_lists.modal_add_redirect")
     */
    public function modalAddRedirectMailingListAction(Request $request) {

        $form   = $this->createForm(RedirectMailingListType::class, new RedirectMailingList());

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()) {

            $em         = $this->get('doctrine.orm.entity_manager');
            $em->persist($form->getData());
            $em->flush();

            $this->addFlash("success", "Mailing list ajoutée!");
            return Modal::refresh();
        }

        return $this->render('@NetBSFichier/generic/add_generic.modal.twig', [
            'title' => 'Nouvelle mailing liste de redirection',
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }

}
