<?php

namespace NetBS\FichierBundle\Controller;

use NetBS\CoreBundle\Utils\Modal;
use NetBS\FichierBundle\Form\AttributionType;
use NetBS\FichierBundle\Mapping\BaseAttribution;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/attribution")
 */
class AttributionController extends Controller
{
    /**
     * @Route("/modal/creation/{membreId}", defaults={"membreId"=null}, name="netbs.fichier.attribution.modal_creation")
     * @param Request $request
     * @return Response
     */
    public function modalAddAction(Request $request, $membreId) {

        $config         = $this->get('netbs.fichier.config');
        $attrClass      = $config->getAttributionClass();
        $em             = $this->get('doctrine.orm.entity_manager');

        /** @var BaseAttribution $attribution */
        $attribution    = new $attrClass();

        if($membreId !== null) {

            $membre     = $em->find($config->getMembreClass(), $membreId);

            if(!$membre)
                throw $this->createNotFoundException();

            $attribution->setMembre($membre);
        }

        $form = $this->createForm(AttributionType::class, $attribution);

        $form->handleRequest($request);
        if($form->isValid() && $form->isSubmitted()) {

            $em->persist($form->getData());
            $em->flush();

            $this->addFlash("success", "Attribution ajoutée avec succès");
            return Modal::refresh();
        }

        return $this->render('@NetBSFichier/attribution/create.modal.twig', [
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }
}
