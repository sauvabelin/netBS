<?php

namespace GalerieBundle\Controller;

use GalerieBundle\Exceptions\MappingException;
use GalerieBundle\Model\NCNode;
use GalerieBundle\Service\GalerieMapper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/netBS/galerie/mapping")
 */
class MappingController extends Controller
{
    /**
     * @Route("/map-distant/{directory}", name="netbs.galerie.mapping.map_distant")
     * @param $directory
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mapDirectoryAction($directory) {

        return $this->render('@Galerie/map_distant.html.twig', [
            'directory'     => base64_decode($directory),
        ]);
    }

    /**
     * @Route("/mapping-information", name="netbs.galerie.mapping.mapping_information")
     * @param Request $request
     * @return JsonResponse
     */
    public function mappingInformationAction(Request $request) {

        $bridge     = $this->get('galerie.mapper');
        $directory  = trim(base64_decode($request->get('directory')), '/');
        $data       = $bridge->getInformation($directory);
        $children   = [];

        foreach($data['children'] as $child) {
            $children[] = [
                'path'      => $child,
                'encoded'   => base64_encode($child)
            ];
        }

        return new JsonResponse([
            'children'  => $children,
            'medias'    => $data['medias']
        ]);
    }

    /**
     * @Route("/map-media", name="netbs.galerie.mapping.map_media")
     * @param Request $request
     * @return JsonResponse
     */
    public function mapMediaAction(Request $request) {

        $media      = $request->get('media');
        $mapper     = $this->get('galerie.mapper');

        try {
            $mapper->handle(GalerieMapper::UPDATED, new NCNode($media));
        } catch (MappingException $exception) {
            return new JsonResponse($exception->getMessage(), 403);
        }

        return new JsonResponse(null, 200);
    }
}