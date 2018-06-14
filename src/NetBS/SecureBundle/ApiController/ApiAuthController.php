<?php

namespace NetBS\SecureBundle\ApiController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthController extends Controller
{
    /**
     * @Route("/gettoken", name="netbs_secure_api_gettoken")
     * @Method({"POST"})
     */
    public function getTokenAction()
    {
        return new Response('', 401);
    }
}
