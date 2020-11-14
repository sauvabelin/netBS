<?php

namespace NetBS\FichierBundle\Controller;

use NetBS\CoreBundle\Utils\Modal;
use NetBS\FichierBundle\Form\Contact\AdresseType;
use NetBS\SecureBundle\Voter\CRUD;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdresseController
 * @Route("/adresse")
 */
class AdresseController extends AbstractController
{
    protected function getClass() {
        return $this->get('netbs.fichier.config')->getAdresseClass();
    }

    /**
     * @Route("/delete/{ownerType}/{ownerId}/{adresseId}", name="netbs.fichier.adresse.delete")
     * @param $ownerType
     * @param $ownerId
     * @return Response
     */
    public function deleteAdresseAction($ownerType, $ownerId, $adresseId) {

        $class  = $this->getClass();
        $em     = $this->get('doctrine.orm.entity_manager');
        $owner  = $em->getRepository(base64_decode($ownerType))->find($ownerId);
        $adrss  = $em->getRepository($class)->find($adresseId);

        if(!$this->isGranted(CRUD::DELETE, $adrss))
            throw $this->createAccessDeniedException("Vous n'avez pas le droit de supprimer cette adresse.");

        $owner->removeAdresse($adrss);
        $em->remove($adrss);
        $em->flush();

        $this->addFlash("info", "Adresse supprimée avec succès");

        return $this->get('netbs.core.history')->getPreviousRoute();
    }

    /**
     * @Route("/modal/creation/{ownerType}/{ownerId}", name="netbs.fichier.adresse.modal_creation")
     * @return Response
     */
    public function modalCreationAction($ownerType, $ownerId, Request $request) {

        $class  = $this->getClass();
        $form   = $this->createForm(AdresseType::class, new $class());
        $em     = $this->get('doctrine.orm.entity_manager');
        $holder = $em->getRepository(base64_decode($ownerType))->find($ownerId);

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()) {

            if(!$this->isGranted(CRUD::UPDATE, $holder))
                throw $this->createAccessDeniedException("Vous n'avez pas le droit d'ajouter d'adresse ici.");

            $holder->addAdresse($form->getData());

            $em->persist($holder);
            $em->flush();

            $this->addFlash("success", "Adresse ajoutée avec succès");
            return Modal::refresh();
        }

        return $this->render('@NetBSFichier/adresse/create.modal.twig', [
            'form'  => $form->createView(),
            'item'  => $holder
        ], Modal::renderModal($form));
    }
}
