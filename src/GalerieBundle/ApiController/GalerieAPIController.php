<?php

namespace GalerieBundle\ApiController;

use GalerieBundle\Model\GalerieMarkdownParser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
     * @Route("/api/v1/netBS/galerie/directory", name="netbs.galerie.api.directory")
     */
    public function getDirectoryAction(Request $request) {

        return $this->generateDirectoryResponse($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/api/v1/public/netBS/galerie/directory", name="netbs.galerie.api.token.directory")
     */
    public function getAccessTokenDirectoryAction(Request $request) {

        $token          = $request->headers->get('x-authorization');
        $token          = str_replace("Bearer ", "", $token);
        $actualToken    = $this->get('netbs.params')->getValue('galerie', 'access_token');

        if($token !== $actualToken)
            return new JsonResponse("access denied", 401);

        return $this->generateDirectoryResponse($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    private function generateDirectoryResponse(Request $request) {

        $path       = urldecode($request->get('path'));
        $path       = trim($path) === "" || $path === null || $path === "root" ? "galerie/" : $path;
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
