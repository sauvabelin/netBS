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
 * @Route("/api/netBS/galerie")
 */
class GalerieAPIController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     * @internal param $path
     * @Route("/directory", name="netbs.galerie.api.directory")
     */
    public function getDirectoryAction(Request $request) {

        $path       = $request->get('directoryPath');
        $path       = trim($path) === "" || $path === null ? "files/" : $path;
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
