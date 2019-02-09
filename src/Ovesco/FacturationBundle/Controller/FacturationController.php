<?php

namespace Ovesco\FacturationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class FacturationController extends Controller
{
    /**
     * @Route("/dashboard", name="ovesco.facturation.dashboard")
     */
    public function dashboardAction() {

        return $this->render('@OvescoFacturation/dashboard.html.twig');
    }
}