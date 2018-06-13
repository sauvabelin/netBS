<?php

namespace GalerieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/netBS/galerie")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="netbs.galerie.admin.dashboard")
     * @return Response
     */
    public function dashboardAction()
    {
        $em     = $this->getDoctrine()->getManager();
        return $this->render('@Galerie/dashboard.html.twig', [

            'countDirectories'  => $this->countEntites("GalerieBundle:Directory"),
            'countMedias'       => $this->countEntites("GalerieBundle:Media")
        ]);
    }

    /**
     * @Route("/directories", name="netbs.galerie.admin.directories")
     * @return Response
     */
    public function manageDirectoriesAction() {

        return $this->render('@Galerie/manage_directories.html.twig');
    }

    /**
     *
     */
    public function mapDirectory() {

    }

    private function countEntites($namespace) {

        return $this->get('doctrine.orm.entity_manager')
            ->createQueryBuilder()
            ->select('COUNT(x)')
            ->from($namespace, 'x')
            ->getQuery()
            ->getScalarResult();
    }
}
