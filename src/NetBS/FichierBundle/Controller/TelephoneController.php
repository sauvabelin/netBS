<?php

namespace NetBS\FichierBundle\Controller;

use NetBS\CoreBundle\Utils\Modal;
use NetBS\FichierBundle\Entity\Telephone;
use NetBS\FichierBundle\Form\Contact\TelephoneType;
use NetBS\SecureBundle\Voter\CRUD;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package FichierBundle\Controller
 * @Route("/telephone")
 */
class TelephoneController extends Controller
{
    /**
     * @Route("/delete/{ownerType}/{ownerId}/{telephoneId}", name="netbs.fichier.telephone.delete")
     * @param $ownerType
     * @param $ownerId
     * @param $telephoneId
     * @return Response
     */
    public function deleteTelephoneAction($ownerType, $ownerId, $telephoneId) {

        $class  = $this->get('netbs.fichier.config')->getTelephoneClass();
        $em     = $this->get('doctrine.orm.entity_manager');
        $owner  = $em->getRepository(base64_decode($ownerType))->find($ownerId);
        $tel    = $em->getRepository($class)->find($telephoneId);

        if(!$this->isGranted(CRUD::DELETE, $tel))
            throw $this->createAccessDeniedException("Suppression du numéro de téléphone refusée");

        $owner->removeTelephone($tel);
        $em->remove($tel);
        $em->flush();

        $this->addFlash("info", "Numéro " . $tel->getTelephone() . " correctement supprimé");
        return $this->get('netbs.core.history')->getPreviousRoute();
    }

    /**
     * @Route("/modal/creation/{ownerType}/{ownerId}", name="netbs.fichier.telephone.modal_creation")
     * @return Response
     */
    public function modalAddAction($ownerType, $ownerId, Request $request) {

        $class  = $this->get('netbs.fichier.config')->getTelephoneClass();
        $form   = $this->createForm(TelephoneType::class, new $class());

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()) {

            $em     = $this->get('doctrine.orm.entity_manager');
            $holder = $em->getRepository(base64_decode($ownerType))->find($ownerId);

            if(!$this->isGranted(CRUD::UPDATE, $holder))
                throw $this->createAccessDeniedException("Accès refusé");

            $holder->addTelephone($form->getData());

            $em->persist($holder);
            $em->flush();

            $this->addFlash("success", "Numéro de téléphone ajouté avec succès");
            return Modal::refresh();
        }

        return $this->render('@NetBSFichier/telephone/add_telephone.modal.twig', [
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }
}
