<?php

namespace GalerieBundle\Controller;

use GalerieBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/yoloswag")
     */
    public function indexAction()
    {
        $fs     = $this->get('oneup_flysystem.nextcloud_webdav_filesystem');
        $mapper = $this->get('netbs.galerie.mapper');
        $media  = new Media();
        $media->setWebdavUrl("files/galerie/clan/beyonce.jpg");

        dump($fs->get($media->getsearchPath()));

        return new Response();
    }
}
