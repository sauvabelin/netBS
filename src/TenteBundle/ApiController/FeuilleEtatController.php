<?php

namespace TenteBundle\ApiController;

use NetBS\FichierBundle\Mapping\BaseGroupe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use TenteBundle\Entity\FeuilleEtat;

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

    /**
     * @Route("/groupes", name="tente.api.tente_unites")
     */
    public function listUnitesAction(Request $request) {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $config = $this->get('netbs.fichier.config');
        $groupes = $em->getRepository($config->getGroupeClass())->findAll();
        $result = array_map(function(BaseGroupe $groupe) {
            return ['id' => $groupe->getId(), 'nom' => $groupe->getNom()];
        }, $groupes);
        return new JsonResponse($result);
    }

    /**
     * @Route("/submit", name="tente.api.submit", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function submitFormAction(Request $request) {

        $em = $this->get('doctrine.orm.default_entity_manager');
        $content = json_decode($request->getContent(), true);

        $feuilleEtat = new FeuilleEtat();
        $identite = $content[0];
        $tente = $em->getRepository('TenteBundle:Tente')->findOneBy(['numero' => $identite['numero']]);
        $unite = $em->find('SauvabelinBundle:BSGroupe', $identite['unite']);

        $feuilleEtat->setTente($tente);
        $feuilleEtat->setGroupe($unite);
        $feuilleEtat->setUser($this->getUser());
        $feuilleEtat->setDebut(new \DateTime($identite['dateDebut']));
        $feuilleEtat->setFin(new \DateTime($identite['dateFin']));
        $feuilleEtat->setActivity($identite['activite']);

        $drawings = [];
        $steps = [];

        for($i = 1; $i < count($content); $i++) {
            $item = $content[$i];
            if (in_array('drawing', array_keys($item)) && in_array('remarques', array_keys($item)))
                $drawings[] = $item;
            else $steps[] = $item;
        }

        $feuilleEtat->setDrawingData(json_encode($drawings));
        $feuilleEtat->setFormData(json_encode($steps));

        $em->persist($feuilleEtat);
        $em->flush();
        return new JsonResponse(['result' => 'OK']);
    }
}
