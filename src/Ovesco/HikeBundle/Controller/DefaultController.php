<?php

namespace Ovesco\HikeBundle\Controller;

use Ovesco\HikeBundle\Service\KMLMerger;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $merger = new KMLMerger();
        $merger->merge(
            __DIR__ . "/../Tests/data/kml1.kml",
            __DIR__ . "/../Tests/data/kml2.kml"
        );

        die;
        return $this->render('OvescoHikeBundle:Default:index.html.twig');
    }
}
