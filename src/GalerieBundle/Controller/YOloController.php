<?php

namespace GalerieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class YOloController extends Controller
{
    /**
     * @Route("/yoloswag")
     */
    public function indexAction()
    {
        $client     = $this->get('webdav.nextcloud_client');
        $content    = $client->request("GET", "galerie/clan/yoloswag/doc");
        dump($content);
        die;
    }
}
