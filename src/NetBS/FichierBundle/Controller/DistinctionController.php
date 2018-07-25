<?php

namespace NetBS\FichierBundle\Controller;

use NetBS\CoreBundle\Utils\Modal;
use NetBS\FichierBundle\Form\DistinctionType;
use NetBS\FichierBundle\Mapping\BaseDistinction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DistinctionController
 * @Route("/distinction")
 */
class DistinctionController extends Controller
{
    /**
     * @Route("/manage", name="netbs.fichier.distinction.page_distinctions")
     * @Security("is_granted('ROLE_READ_EVERYWHERE')")
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
     * @return Response
     * @Route("/modal/add", name="netbs.fichier.distinction.modal_add")
     * @Security("has_role('ROLE_CREATE_EVERYWHERE')")
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