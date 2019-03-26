<?php

namespace SauvabelinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package SauvabelinBundle\Controller
 * @Route("/test")
 */
class TestController extends Controller
{
    /**
     * @Route("/test")
     */
    public function test() {

        return new Response();
    }

}
