<?php

namespace NetBS\FichierBundle\Controller;

use NetBS\FichierBundle\Form\Personne\GeniteurType;
use NetBS\FichierBundle\Mapping\BaseGeniteur;
use NetBS\SecureBundle\Voter\CRUD;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DistinctionController
 * @Route("/geniteur")
 */
class GeniteurController extends AbstractController
{
    /**
     * @Route("/create-for-famille/{id}", name="netbs.fichier.geniteur.create")
     */
    public function createGeniteurAction(Request $request, $id) {

        $configurator   = $this->get('netbs.fichier.config');
        $class          = $configurator->getGeniteurClass();
        $familleClass   = $configurator->getFamilleClass();
        $em             = $this->get('doctrine.orm.entity_manager');
        $famille        = $em->find($familleClass, $id);

        if(!$famille)
            throw $this->createNotFoundException();

        if(!$this->isGranted(CRUD::UPDATE, $famille))
            throw $this->createAccessDeniedException("Ajout de géniteur refusé");

        /** @var BaseGeniteur $geniteur */
        $geniteur       = $configurator->createGeniteur();
        $geniteur->setFamille($famille);

        $form       = $this->createForm(GeniteurType::class, $geniteur);
        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($geniteur);
            $em->flush();

            $this->addFlash("success", "{$geniteur->__toString()} ajouté!");
            return $this->redirectToRoute('netbs.fichier.famille.page_famille', array('id' => $famille->getId()));
        }

        return $this->render('@NetBSFichier/geniteur/add_geniteur.html.twig', [

            'header'    => "Nouveau représentant légal",
            'famille'   => $famille,
            'subHeader' => "Ajouter un représentant légal à la " . $famille->__toString(),
            'form'      => $form->createView()
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/remove/{id}", name="netbs.fichier.geniteur.remove")
     */
    public function removeGeniteurAction($id) {

        $configurator   = $this->get('netbs.fichier.config');
        $class          = $configurator->getGeniteurClass();
        $em             = $this->get('doctrine.orm.entity_manager');
        $geniteur       = $em->find($class, $id);

        if(!$geniteur)
            throw $this->createNotFoundException();

        if(!$this->isGranted(CRUD::DELETE, $geniteur))
            throw $this->createAccessDeniedException("Suppression de géniteur refusée");

        $fid    = $geniteur->getFamille()->getId();

        $em->remove($geniteur);
        $em->flush();

        $this->addFlash("info", "Représentant légal supprimé");
        return $this->redirectToRoute('netbs.fichier.famille.page_famille', ['id' => $fid]);
    }
}
