<?php

namespace NetBS\CoreBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParameterController extends Controller
{
    /**
     * @Route("/parameters/list", name="netbs.core.parameters.list")
     */
    public function listParametersAction()
    {
        return $this->render('@NetBSCore/parameters/list_parameters.html.twig');
    }
}
