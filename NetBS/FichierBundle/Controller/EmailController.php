<?php

namespace NetBS\FichierBundle\Controller;

use NetBS\CoreBundle\Utils\Modal;
use NetBS\FichierBundle\Form\Contact\BSEmailType;
use NetBS\SecureBundle\Voter\CRUD;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package FichierBundle\Controller
 * @Route("/email")
 */
class EmailController extends AbstractController
{
    /**
     * @Route("/delete/{ownerType}/{ownerId}/{emailId}", name="netbs.fichier.email.delete")
     * @param $ownerType
     * @param $ownerId
     * @param $emailId
     * @return Response
     */
    public function deleteEmailAction($ownerType, $ownerId, $emailId) {

        $class  = $this->get('netbs.fichier.config')->getEmailClass();
        $em     = $this->get('doctrine.orm.entity_manager');
        $owner  = $em->getRepository(base64_decode($ownerType))->find($ownerId);
        $email  = $em->getRepository($class)->find($emailId);

        if(!$this->isGranted(CRUD::DELETE, $email))
            throw $this->createAccessDeniedException("Suppression d'email refusée");

        $owner->removeEmail($email);
        $em->remove($email);
        $em->flush();

        $this->addFlash("info", "Email " . $email->getEmail() . " correctement supprimé");
        return $this->get('netbs.core.history')->getPreviousRoute();
    }

    /**
     * @Route("/modal/creation/{ownerType}/{ownerId}", name="netbs.fichier.email.modal_creation")
     * @param $ownerType
     * @param $ownerId
     * @param Request $request
     * @return Response
     */
    public function modalAddAction($ownerType, $ownerId, Request $request) {

        $class  = $this->get('netbs.fichier.config')->getEmailClass();
        $form   = $this->createForm(BSEmailType::class, new $class());

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()) {

            $em     = $this->get('doctrine.orm.entity_manager');
            $holder = $em->getRepository(base64_decode($ownerType))->find($ownerId);
            $holder->addEmail($form->getData());

            if(!$this->isGranted(CRUD::UPDATE, $holder))
                throw $this->createAccessDeniedException("Ajout d'email refusé");

            $em->persist($holder);
            $em->flush();

            $this->addFlash('success', "L'email a été ajoutée");
            return Modal::refresh();
        }

        return $this->render('@NetBSFichier/email/add_email.modal.twig', [
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }
}
