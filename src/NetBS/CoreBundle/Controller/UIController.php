<?php

namespace NetBS\CoreBundle\Controller;

use NetBS\FichierBundle\Mapping\BaseFamille;
use NetBS\FichierBundle\Mapping\BaseGroupe;
use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\SecureBundle\Voter\CRUD;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UIController extends Controller
{
    /**
     * @Route("/ui/global-search", name="netbs.core.ui.global_search")
     * @param Request $request
     * @return JsonResponse
     */
    public function globalSearchAction(Request $request)
    {
        $term           = $request->get('query');
        $router         = $this->get('router');
        $membres        = $this->get('netbs.fichier.select2.membre_provider')->search($term, 5);
        $groupes        = $this->get('netbs.fichier.select2.groupe_provider')->search($term, 2);
        $familles       = $this->get('netbs.fichier.select2.famille_provider')->search($term, 3);

        $results        = [];

        /** @var BaseMembre $membre */
        foreach($membres as $membre) {

            if(!$this->isGranted(CRUD::READ, $membre))
                continue;

            $descr      = '';
            if($attr = $membre->getActiveAttribution())
                $descr  = $attr->__toString();

            $results[] = [
                'name'          => $membre->getFullName(),
                'description'   => $descr,
                'path'          => $router->generate('netbs.fichier.membre.page_membre', ['id' => $membre->getId()])
            ];
        }

        /** @var BaseFamille $famille */
        foreach($familles as $famille) {

            if(!$this->isGranted(CRUD::READ, $famille))
                continue;

            $descr  = '';
            if($adresse = $famille->getSendableAdresse())
                $descr = $adresse->getNpa() . ' ' . $adresse->getLocalite();

            $results[] = [
                'name'          => $famille->__toString(),
                'description'   => $descr,
                'path'          => $router->generate('netbs.fichier.famille.page_famille', ['id' => $famille->getId()])
            ];
        }

        /** @var BaseGroupe $groupe */
        foreach($groupes as $groupe) {

            if(!$this->isGranted(CRUD::READ, $groupe))
                continue;

            $results[] = [
                'name'          => $groupe->getNom(),
                'description'   => $groupe->getGroupeType()->getNom(),
                'path'          => $router->generate('netbs.fichier.groupe.page_groupe', ['id' => $groupe->getId()])
            ];
        }

        return new JsonResponse($results);
    }
}
