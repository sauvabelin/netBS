<?php

namespace GalerieBundle\Controller;

use GalerieBundle\Model\GalerieMarkdownParser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GalerieAPIController
 * @package GalerieBundle\Controller
 */
class GalerieAPIController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     * @internal param $path
     * @Route("/api/netBS/galerie/directory", name="netbs.galerie.api.directory")
     */
    public function getDirectoryAction(Request $request) {

        return $this->generateApiResponse($request);
    }

    /**
     * @param Request $request
     * @Route("/galerie/parent-api-call/directory")
     * @return JsonResponse
     */
    public function parentApiCallAction(Request $request) {

        $token      = $request->get('token');
        $galerie    = $this->get('galerie');

        if($token !== $galerie->getToken())
            throw $this->createAccessDeniedException();

        return $this->generateApiResponse($request);
    }

    private function generateApiResponse(Request $request) {

        $path       = $request->get('path');
        $path       = trim($path) === "" || $path === null || $path === "root" ? "files/" : $path;
        $tree       = $this->get('galerie.tree');
        $repo       = $this->getDoctrine()->getRepository('GalerieBundle:Directory');
        $directory  = $repo->findOneBy(array('webdavUrl' => $path));

        if(!$directory)
            throw $this->createNotFoundException();

        $parser = new GalerieMarkdownParser($directory, $this->get('liip_imagine.cache.manager'));

        return new JsonResponse($this->get('serializer')->serialize([
            'current'       => $directory,
            'description'   => $parser->text($directory->getDescription()),
            'children'      => $tree->getChildren($directory),
            'medias'        => $tree->getMedias($directory)
        ], 'json'), 200, [], true);
    }
}
