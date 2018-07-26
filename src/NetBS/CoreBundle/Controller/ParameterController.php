<?php

namespace NetBS\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParameterController extends Controller
{
    /**
     * @Route("/parameters/list", name="netbs.core.parameters.list")
     */
    public function listParametersAction()
    {
        dump($this->get('security.token_storage')->getToken()->getRoles());
        return $this->render('@NetBSCore/parameters/list_parameters.html.twig');
    }
}
