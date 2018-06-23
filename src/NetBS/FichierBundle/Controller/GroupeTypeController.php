<?php

namespace NetBS\FichierBundle\Controller;

use NetBS\CoreBundle\Utils\Modal;
use NetBS\FichierBundle\Form\GroupeTypeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/groupe-type")
 */
class GroupeTypeController extends Controller
{
    /**
     * @Route("/manage", name="netbs.fichier.groupe_type.page_groupe_types")
     * @Security("is_granted('ROLE_SG')")
     */
    public function pageGroupeTypesAction() {

        return $this->render('@NetBSFichier/generic/page_generic.html.twig', array(
            'list'      => 'netbs.fichier.groupe_types',
            'title'     => "Types de groupe",
            'subtitle'  => 'Tous les types enregistrés',
            'modalPath' => $this->get('router')->generate('netbs.fichier.groupe_type.modal_add')
        ));
    }

    /**
     * @param Request $request
     * @Route("/modal/add", name="netbs.fichier.groupe_type.modal_add")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('ROLE_SG')")
     */
    public function addGroupeTypeModalAction(Request $request) {

        $config         = $this->get('netbs.fichier.config');
        $class          = $config->getGroupeTypeClass();
        $gtype          = new $class();
        $form           = $this->createForm(GroupeTypeType::class, $gtype);

        $form->handleRequest($request);
        if($form->isValid() && $form->isSubmitted()) {

            $em         = $this->get('doctrine.orm.entity_manager');
            $em->persist($form->getData());
            $em->flush();

            $this->addFlash('success', "Type de groupe ajouté");
            return Modal::refresh();
        }

        return $this->render('@NetBSFichier/generic/add_generic.modal.twig', [
            'form'  => $form->createView(),
            'title' => 'Nouveau type de groupe'
        ], Modal::renderModal($form));
    }
}