<?php

namespace GalerieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/netBS/galerie/mapping")
 */
class MappingController extends Controller
{
    /**
     * @Route("/map-distant/{directory}", name="netbs.galerie.mapping.map_distant")
     */
    public function mapDirectoryAction($directory) {

        $bridge = $this->get('galerie.nextcloud_bridge');

        return $this->render('@Galerie/map_distant.html.twig', [

            'directory'     => base64_decode($directory),
            'data'          => $bridge->getInformation(trim(base64_decode($directory), '/'))
        ]);
    }

    /**
     * @Route("/mapping-information", name="netbs.galerie.mapping.mapping_information")
     */
    public function mappingInformationAction(Request $request) {

        $directory  = base64_decode($request->get('directory'));

    }
}