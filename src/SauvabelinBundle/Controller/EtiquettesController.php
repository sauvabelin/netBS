<?php

namespace SauvabelinBundle\Controller;

use NetBS\FichierBundle\Mapping\BaseAttribution;
use NetBS\FichierBundle\Mapping\BaseGroupe;
use NetBS\SecureBundle\Voter\CRUD;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EtiquettesController extends Controller
{
    /**
     * @Route("/etiquettes/groupe/{id}", name="sauvabelin.etiquettes.groupe")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function etiquettesGroupeAction(Request $request, $id) {

        $em     = $this->get('doctrine.orm.default_entity_manager');
        $config = $this->get('netbs.fichier.config');

        /** @var BaseGroupe $groupe */
        $groupe = $em->find($config->getGroupeClass(), $id);
        if(!$this->isGranted(CRUD::READ, $groupe))
            throw $this->createAccessDeniedException("Vous n'avez pas le droit d'imprimer des étiquettes pour ce groupe.");

        $ids    = array_map(function(BaseAttribution $attribution) { return $attribution->getMembre()->getId(); },
            $groupe->getActivesRecursivesAttributions());

        return $this->redirectToRoute('netbs.core.export.export_selected', ['data' => json_encode([
            'itemsClass'    => base64_encode($config->getMembreClass()),
            'selectedIds'   => $ids,
            'exporterAlias' => 'pdf.etiquettes'
        ])]);
    }
}