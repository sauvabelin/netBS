<?php

namespace NetBS\FichierBundle\Controller;

use NetBS\CoreBundle\Utils\Modal;
use NetBS\FichierBundle\Form\GroupeCategorieType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DistinctionController
 * @Route("/groupe-categorie")
 */
class GroupeCategorieController extends Controller
{
    /**
     * @Route("/manage", name="netbs.fichier.groupe_categorie.page_groupe_categories")
     * @Security("is_granted('ROLE_READ_EVERYWHERE')")
     */
    public function pageGroupeCategorieAction() {

        return $this->render('@NetBSFichier/generic/page_generic.html.twig', array(
            'list'      => 'netbs.fichier.groupe_categories',
            'title'     => "Catégories d'unités",
            'subtitle'  => 'Toutes les catégories enregistrés',
            'modalPath' => $this->get('router')->generate('netbs.fichier.groupe_categorie.modal_add')
        ));
    }

    /**
     * @param Request $request
     * @Route("/modal/add", name="netbs.fichier.groupe_categorie.modal_add")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('ROLE_CREATE_EVERYWHERE')")
     */
    public function addGroupeCategorieModalAction(Request $request) {

        $config         = $this->get('netbs.fichier.config');
        $class          = $config->getGroupeCategorieClass();
        $gcategorie     = new $class();
        $form           = $this->createForm(GroupeCategorieType::class, $gcategorie);

        $form->handleRequest($request);
        if($form->isValid() && $form->isSubmitted()) {

            $em         = $this->get('doctrine.orm.entity_manager');
            $em->persist($form->getData());
            $em->flush();

            $this->addFlash("success", "Catégorie de groupe ajoutée");
            return Modal::refresh();
        }

        return $this->render('@NetBSFichier/generic/add_generic.modal.twig', [
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }
}