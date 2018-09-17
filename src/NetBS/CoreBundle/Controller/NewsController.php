<?php

namespace NetBS\CoreBundle\Controller;

use NetBS\CoreBundle\Entity\News;
use NetBS\CoreBundle\Entity\NewsChannel;
use NetBS\CoreBundle\Form\NewsChannelType;
use NetBS\CoreBundle\Form\NewsType;
use NetBS\CoreBundle\Utils\Modal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @Route("/manage", name="netbs.core.news.manage")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manageNewsAction(Request $request) {

        return $this->render("@NetBSCore/news/manage_news.html.twig", [

        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @route("/modal/add-channel", name="netbs.core.news.modal_add_channel")
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
     * @Route("/modal-add-edit-news/{id}", defaults={"id"=null}, name="netbs.core.news.modal_edit_news")
     */
    public function modalAddEditNewsAction(Request $request, $id) {

        $em     = $this->get('doctrine.orm.entity_manager');
        $title  = $id ? "Modifier" : "Publier";
        $news   = new News();

        if($id)
            $news = $em->find('NetBSCoreBundle:News', $id);
        else
            $news->setUser($this->getUser());

        $form   = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            /** @var News $news */
            $news       = $form->getData();

            $em->persist($news);
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