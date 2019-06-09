<?php

namespace TDGLBundle\Controller;

use NetBS\FichierBundle\Mapping\BaseFamille;
use NetBS\SecureBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TDGLBundle\Form\InscriptionType;
use TDGLBundle\Model\Inscription;

class MembreController extends Controller
{
    /**
     * @param Request $request
     * @Route("/membre/nouveau", name="tdgl.membre.add_membre")
     * @return Response
     */
    public function pageAddMembreAction(Request $request) {

        /** @var User $user */
        $user = $this->getUser();
        $config = $this->get('netbs.fichier.config');
        $infos = new Inscription();
        $em = $this->get('doctrine.orm.entity_manager');
        $selectedFamilyId = $request->request->get('circulaire_membre')['familleId'];
        $form = $this->createForm(InscriptionType::class, $infos);

        $form->handleRequest($request);

        if(!empty($selectedFamilyId))
            $infos->famille = $em->find($config->getFamilleClass(), intval($selectedFamilyId));
        else $infos->generateFamille();

        if($form->isSubmitted() && $form->isValid()) {

            $membre     = $infos->generateMembre();
            $famille    = $infos->generateFamille();
            $famille->addMembre($membre);

            $em->persist($famille);
            $em->flush();

            return $this->redirect($this->generateUrl('netbs.fichier.membre.page_membre', array('id' => $membre->getId())));
        }

        return $this->render('@TDGL/membre/nouveau.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @Route("/search", name="sauvabelin.famille.search")
     * @return Response
     */
    public function searchFamilleAction(Request $request) {

        $term       = $request->get('term');
        $provider   = $this->get('netbs.fichier.select2.famille_provider');
        $results    = $provider->search($term, 5);
        $serializer = $this->get('serializer');

        $response   = new Response($serializer->serialize($results, 'json', array(
            'groups'  => ['default', 'familleMembres', 'familleAdresse']
        )));

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}

