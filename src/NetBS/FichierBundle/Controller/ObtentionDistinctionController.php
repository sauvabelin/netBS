<?php

namespace NetBS\FichierBundle\Controller;

use NetBS\CoreBundle\Utils\Modal;
use NetBS\FichierBundle\Form\ObtentionDistinctionType;
use NetBS\FichierBundle\Mapping\BaseObtentionDistinction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/obtention-distinction")
 */
class ObtentionDistinctionController extends Controller
{
    /**
     * @Route("/modal/creation/{membreId}", defaults={"membreId"=null}, name="netbs.fichier.obtention_distinction.modal_creation")
     * @param Request $request
     * @param $membreId
     * @return Response
     */
    public function modalAddAction(Request $request, $membreId) {

        $em             = $this->get('doctrine.orm.entity_manager');
        $config         = $this->get('netbs.fichier.config');
        $odClass        = $config->getObtentionDistinctionClass();

        /** @var BaseObtentionDistinction $od */
        $od             = new $odClass();
        $membre         = $em->find($config->getMembreClass(), $membreId);

        if(!$membre)
            throw $this->createNotFoundException();

        $od->setMembre($membre);

        $form           = $this->createForm(ObtentionDistinctionType::class, $od);

        $form->handleRequest($request);
        if($form->isValid() && $form->isSubmitted()) {

            $em->persist($form->getData());
            $em->flush();

            $this->addFlash("success", "Distinction ajoutée avec succès");
            return Modal::refresh();
        }

        return $this->render('@NetBSFichier/generic/add_generic.modal.twig', [
            'title' => "Nouvelle distinction",
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }
}
