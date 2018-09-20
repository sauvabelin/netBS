<?php

namespace Ovesco\GalerieBundle\ApiController;

use Ovesco\GalerieBundle\Model\Directory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GalerieAPIController
 * @package GalerieBundle\Controller
 */
class ApiGalerieController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/api/v1/public/netBS/galerie/directory", name="ovesco.galerie.public_api.directory")
     */
    public function publicAccessAction(Request $request) {

        $token          = $request->headers->get('x-authorization');
        $token          = str_replace("Bearer ", "", $token);
        $actualToken    = $this->get('netbs.params')->getValue('galerie', 'parent_token');

        if($token !== $actualToken)
            return new JsonResponse("access denied", 401);

        return $this->generateDirectoryResponse($request);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/api/v1/netBS/galerie/directory", name="ovesco.galerie.api.directory")
     */
    public function getDirectoryAction(Request $request) {

        return $this->generateDirectoryResponse($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    private function generateDirectoryResponse(Request $request) {

        $config         = $this->get('ovesco.galerie.config');
        $path           = urldecode($request->get('path'));
        $realPath       = $config->getFullMappedDirectory() . (empty($path) ? "" : "/" . trim($path, "/"));

        if(!is_dir($realPath))
            throw $this->createNotFoundException("Directory with path $path not found");

        $directory      = new Directory($realPath, $config);

        $data = [
            'name'          => $directory->getName(),
            'path'          => $directory->getRelativePath(),
            'description'   => $directory->getDescription(),
            'thumbnail'     => $directory->getThumbnail(),
            'children'      => $directory->getChildren(),
            'medias'        => $directory->getMedias()
        ];

        return new JsonResponse($this->get('serializer')->serialize($data, 'json'), 200, [], true);
    }
}
