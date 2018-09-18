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
     * @Route("/test", name="netbs.galerie.api.directory")
     */
    public function getDirectoryAction(Request $request) {

        return $this->generateDirectoryResponse($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    private function generateDirectoryResponse(Request $request) {

        $cacheService   = $this->get('liip_imagine.cache.manager');
        $cachePrefix    = $this->getParameter('ovesco.galerie.cache_prefix');
        $config         = $this->get('ovesco.galerie.config');
        $path           = urldecode($request->get('path'));
        $realPath       = trim($config->getPath() . "/" . trim($path, '/'), '/');

        if(!is_dir($realPath))
            throw $this->createNotFoundException("Directory with path $path not found");

        $directory      = new Directory($realPath, $config);
        $children       = [];
        $medias         = [];

        foreach($directory->getChildren() as $child) {

            $children[] = [
                'nom' => $child->getName(),
                'thumbnail' => $this->getWebPath($cacheService->getBrowserPath($child->getThumbnail()->getCachePath(), 'thumbnail')),
            ];
        }

        foreach($directory->getMedias() as $media) {
            $medias[]       = [
                'nom'       => $media->getName(),
                'path'      => $this->getWebPath($cachePrefix . "/" . $media->getPath()),
                'thumbnail' => $this->getWebPath($cacheService->getBrowserPath($media->getCachePath(), 'thumbnail')),
                'size'      => $media->getSize(),
                'date'      => $media->getTimestamp()
            ];
        }

        $data = [
            'nom'       => $directory->getName(),
            'thumbnail' => $this->getWebPath($cacheService->getBrowserPath($directory->getThumbnail()->getCachePath(), 'thumbnail')),
            'children'  => $children,
            'medias'    => $medias
        ];

        return new JsonResponse($data);
    }

    private function getWebPath($path) {

        $request    = $this->get('request_stack')->getCurrentRequest();

        return $request->getSchemeAndHttpHost() . $this->get('twig.extension.assets')->getAssetUrl($path);
    }
}
