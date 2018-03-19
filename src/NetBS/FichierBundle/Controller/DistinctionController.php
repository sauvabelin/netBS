<?php

namespace NetBS\FichierBundle\Controller;

use NetBS\CoreBundle\Utils\Modal;
use NetBS\FichierBundle\Form\DistinctionType;
use NetBS\FichierBundle\Mapping\BaseDistinction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DistinctionController
 * @Route("/distinction")
 */
class DistinctionController extends Controller
{
    /**
     * @Route("/manage", name="netbs.fichier.distinction.page_distinctions")
     */
    public function pageDistinctionsAction() {

        return $this->render('@NetBSFichier/generic/page_generic.html.twig', array(
            'list'      => 'netbs.fichier.distinctions',
            'title'     => 'Distinctions',
            'subtitle'  => 'Toutes les distinctions enregistrées',
            'modalPath' => $this->get('router')->generate('netbs.fichier.distinction.modal_add')
        ));
    }

    /**
     * @param Request $request
     * @Route("/modal/add", name="netbs.fichier.distinction.modal_add")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addDistinctionModalAction(Request $request) {

        $config         = $this->get('netbs.fichier.config');
        $distClass      = $config->getDistinctionClass();

        /** @var BaseDistinction $distinction */
        $distinction    = new $distClass();
        $form           = $this->createForm(DistinctionType::class, $distinction);

        $form->handleRequest($request);
        if($form->isValid() && $form->isSubmitted()) {

            $em         = $this->get('doctrine.orm.entity_manager');
            $em->persist($form->getData());
            $em->flush();

            $this->addFlash('success', "La distinction {$distinction->getNom()} a été ajoutée");
            return Modal::refresh();
        }

        return $this->render('@NetBSFichier/generic/add_generic.modal.twig', [
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }
}