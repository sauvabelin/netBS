<?php

namespace TenteBundle\ApiController;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FeuilleEtatController
 * @package TenteBundle\Controller
 * @Route("/feuille-etat")
 */
class FeuilleEtatController extends Controller
{
    /**
     * @Route("/tente-model-form", name="tente.api.tente_model_form")
     */
    public function listModelsAction(Request $request) {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $models = $em->getRepository('TenteBundle:TenteModel')->findAll();

        return new JsonResponse($this->get('serializer')->serialize($models, 'json'), 200, [], true);
    }
}
