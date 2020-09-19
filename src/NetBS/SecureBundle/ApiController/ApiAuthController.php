<?php

namespace NetBS\SecureBundle\ApiController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthController extends Controller
{
    /**
     * @Route("/gettoken", name="netbs_secure_api_gettoken", methods={"POST"})
     */
    public function getTokenAction()
    {
        return new Response('', 401);
    }
}
