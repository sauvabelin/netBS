<?php

namespace SauvabelinBundle\Controller;

use NetBS\FichierBundle\Mapping\BaseFamille;
use SauvabelinBundle\Entity\BSUser;
use SauvabelinBundle\Form\CirculaireMembreType;
use SauvabelinBundle\Model\CirculaireMembre;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MembreController extends Controller
{
    /**
     * @param Request $request
     * @Route("/membre/nouveau", name="sauvabelin.membre.add_membre")
     * @return Response
     */
    public function pageAddMembreAction(Request $request) {

        /** @var BSUser $user */
        $user               = $this->getUser();
        $config             = $this->get('netbs.fichier.config');
        $infos              = new CirculaireMembre();
        $em                 = $this->get('doctrine.orm.entity_manager');
        $previousNumber     = $em->createQueryBuilder()
            ->select('m')->from($config->getMembreClass(), 'm')
            ->orderBy('m.numeroBS', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
            ->getNumeroBS();

        $infos->numero      = $previousNumber + 1;
        $form               = $this->createForm(CirculaireMembreType::class, $infos);
        $selectedFamilyId   = $request->request->get('circulaire_membre')['familleId'];
        $selectedFamily     = $infos->generateFamille();

        if($selectedFamilyId) {

            $selectedFamily = $em->find($config->getFamilleClass(), intval($selectedFamilyId));
            $infos->setFamille($selectedFamily);
        }
        else {
            $selectedFamily->setNom($infos->nom);
        }

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            $membre     = $infos->getMembre();
            $famille    = $infos->generateFamille();

            if($user->hasRole('ROLE_SG'))
                $famille->setValidity(BaseFamille::VALIDE);

            $famille->addMembre($membre);

            $em->persist($famille);
            $em->flush();

            return $this->redirect($this->generateUrl('netbs.fichier.membre.page_membre', array('id' => $membre->getId())));
        }

        return $this->render('@Sauvabelin/membre/nouveau.html.twig', array(
            'form'              => $form->createView()
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
            'groups'    => ['default', 'familleMembres', 'familleAdresse', 'familleGeniteurs']
        )));

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
