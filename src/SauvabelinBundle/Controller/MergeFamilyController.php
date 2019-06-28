<?php

namespace SauvabelinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package SauvabelinBundle\Controller
 * @Route("/merge-family")
 */
class MergeFamilyController extends Controller
{
    /**
     * @Route("/merger", name="sauvabelin.merge_family.merger")
     */
    public function mergerAction() {

        $config = $this->get('netbs.fichier.config');
        $repo = $this->getDoctrine()->getRepository($config->getFamilleClass());

        return $this->render('@Sauvabelin/mergeFamily/merger.html.twig', [
            'familles' => $repo->findAll()
        ]);
    }

    /**
     * @Route("/choose-what", name="sauvabelin.merge_family.choose_what")
     */
    public function chooseWhatAction(Request $request) {

        $config = $this->get('netbs.fichier.config');
        $repo = $this->get('doctrine.orm.default_entity_manager')->getRepository($config->getFamilleClass());
        $familles = array_map(function($id) use ($repo) {
            return $repo->find($id);
        }, $request->request->get('famille'));

        return $this->render('@Sauvabelin/mergeFamily/choose_what.html.twig', [
            'familles' => $familles,
        ]);
    }
}
