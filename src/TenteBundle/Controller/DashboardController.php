<?php

namespace TenteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="tente.dashboard")
     */
    public function indexAction()
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        return $this->render('@Tente/Default/dashboard.html.twig');
    }
}
