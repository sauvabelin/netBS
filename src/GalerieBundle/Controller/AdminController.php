<?php

namespace GalerieBundle\Controller;

use GalerieBundle\Entity\Directory;
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
        $mapper = $this->get('galerie.mapper');
        $mapper->fullMapDirectory('galerie/futon/swag/yolo/unit10/');

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

    public function mapDirectoryAction() {


    }

    /**
     * @param Directory $directory
     * @Route("/directory/remove/{directory}", name="netbs.galerie.admin.remove_directory")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeDirectoryAction(Directory $directory) {

        $mapper = $this->get('galerie.mapper');
        $mapper->removeDirectory($directory);

        return $this->redirectToRoute('netbs.galerie.admin.directories');
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
