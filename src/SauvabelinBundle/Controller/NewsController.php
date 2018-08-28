<?php

namespace SauvabelinBundle\Controller;

use NetBS\CoreBundle\Utils\Modal;
use SauvabelinBundle\Entity\News;
use SauvabelinBundle\Entity\NewsChannel;
use SauvabelinBundle\Form\NewsChannelType;
use SauvabelinBundle\Form\NewsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class NewsController
 * @package SauvabelinBundle\Controller
 * @Route("/news")
 */
class NewsController extends Controller
{
    /**
     * @param Request $request
     * @Route("/manage", name="sauvabelin.news.manage")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manageNewsAction(Request $request) {

        return $this->render("@Sauvabelin/news/manage_news.html.twig", [

        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @route("/modal/add-channel", name="sauvabelin.news.modal_add_channel")
     */
    public function addNewsChannelModalAction(Request $request) {

        $form   = $this->createForm(NewsChannelType::class, new NewsChannel());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($form->getData());
            $em->flush();

            $this->addFlash("success", "Channel ajoutée");
            return Modal::refresh();
        }

        return $this->render('@NetBSFichier/generic/add_generic.modal.twig', [
            'title' => 'Ajouter une channel de news',
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/modal/edit-news/{id}", defaults={"id"=null}, name="sauvabelin.news.edit_news")
     */
    public function editNewsModalAction(Request $request, $id) {

        $em     = $this->get('doctrine.orm.entity_manager');
        $title  = $id ? "Modifier" : "Publier";
        $news   = new News();

        if($id)
            $news = $em->find('SauvabelinBundle:News', $id);
        else
            $news->setUser($this->getUser());

        $form   = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $em->persist($form->getData());
            $em->flush();

            $this->addFlash("success", "News publiée!");
            return Modal::refresh();
        }

        return $this->render('@NetBSFichier/generic/add_generic.modal.twig', [
            'title' => $title . ' une news',
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }
}