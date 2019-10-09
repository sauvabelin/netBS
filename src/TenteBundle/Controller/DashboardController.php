<?php

namespace TenteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use TenteBundle\Entity\Tente;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="tente.dashboard")
     */
    public function indexAction()
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $repo = $em->getRepository('TenteBundle:Tente');

        return $this->render('@Tente/Default/dashboard.html.twig', [
            'tentes' => $repo->count([]),
            'tentesActivite' => $repo->count(['status' => Tente::EN_ACTIVITE]),
            'tentesReparation' => $repo->count(['status' => Tente::EN_REPARATION]),
            'tentesIndisponibles' => $repo->count(['status' => Tente::INDISPONIBLE]),
            'tentesVerifier' => $repo->count(['status' => Tente::A_REPARER]),
        ]);
    }
}
