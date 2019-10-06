<?php

namespace TenteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use TenteBundle\Entity\FeuilleEtat;

/**
 * @package TenteBundle\Controller
 * @Route("/feuille-etat")
 */
class FeuilleEtatController extends Controller
{
    /**
     * @Route("/view/{id}", name="tente.feuille_etat.view")
     */
    public function viewAction(FeuilleEtat $feuilleEtat) {

        return $this->render('@Tente/feuilles/view_feuille.modal.twig', [
            'feuille' => $feuilleEtat
        ]);
    }
}
