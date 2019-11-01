<?php

namespace NetBS\FichierBundle\Controller;

use NetBS\CoreBundle\Block\Model\Tab;
use NetBS\CoreBundle\Block\CardBlock;
use NetBS\CoreBundle\Block\TabsCardBlock;
use NetBS\CoreBundle\Block\TemplateBlock;
use NetBS\CoreBundle\Event\RemoveMembreEvent;
use NetBS\FichierBundle\Form\Personne\MembreType;
use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\SecureBundle\Voter\CRUD;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MembreController
 * @Route("/membre")
 */
class MembreController extends Controller
{
    /**
     * @param int $id
     * @Route("/page/{id}", name="netbs.fichier.membre.page_membre")
     * @return Response
     */
    public function pageMembreAction($id) {

        $config     = $this->get('netbs.fichier.config');
        $exporters  = $this->get('netbs.core.exporter_manager')->getExportersForClass($config->getMembreClass());

        /** @var BaseMembre $membre */
        $membre     = $this->get('doctrine.orm.entity_manager')->find($config->getMembreClass(), $id);
        $form       = $this->createForm(MembreType::class, $membre)->createView();

        if(!$membre)
            throw $this->createNotFoundException();

        if(!$this->isGranted(CRUD::READ, $membre))
            throw $this->createAccessDeniedException("Accès à la page de {$membre->getFullName()} refusé");

        $designer   = $this->get('netbs.core.block.layout');
        $lists      = $this->get('netbs.core.dynamic_list_manager')->getAvailableLists($config->getMembreClass());

        $config     = $designer::configurator()
            ->addRow()
                ->pushColumn(3)
                    ->addRow()
                        ->pushColumn(12)
                            ->setBlock(CardBlock::class, [
                                'template'  => "@NetBSFichier/membre/presentation.block.twig",
                                'title'     => $membre->__toString(),
                                'subtitle'  => ($attr = $membre->getActiveAttribution()) ? $attr : null,
                                'params'    => [
                                    'form'  => $form
                            ]])
                        ->close()
                    ->close()
                    ->addRow()
                        ->pushColumn(12)
                            ->setBlock(TemplateBlock::class, [
                                'template'  => '@NetBSFichier/block/membre_links.block.twig',
                                'params'    => [
                                    'membre'        => $membre,
                                    'lists'         => $lists,
                                    'exporters'     => $exporters,
                                    'exportClass'   => base64_encode($config->getMembreClass()),
                                ]
                            ])
                        ->close()
                    ->close()
                ->close()
                ->pushColumn(9)
                    ->addRow()
                        ->pushColumn(12)
                            ->setBlock(TabsCardBlock::class, ['tabs' => [
                                new Tab('Contact', '@NetBSFichier/block/tabs/sendable_contact.tab.twig', [
                                    'item'          => $membre,
                                    'idealOwner'    => $membre->getFamille()
                                ]),
                                new Tab($membre, '@NetBSFichier/block/tabs/editable_contact.tab.twig', [
                                    'item'  => $membre,
                                    'form'  => $form
                                ]),
                                new Tab('Attributions et distinctions', '@NetBSFichier/block/tabs/attributions_distinctions.tab.twig', [
                                    'membre'    => $membre
                                ])
                            ]])
                        ->close()
                    ->close()
                ->close()
            ->close();

        return $designer->renderResponse('netbs', $config, [
            'title' => $membre->__toString(),
            'item'  => $membre
        ]);
    }


    /**
     * @Route("/search", name="netbs.fichier.membre.search")
     * @return Response
     */
    public function searchMembreAction() {

        $searcher       = $this->get('netbs.core.searcher_manager');
        $instance       = $searcher->bind($this->get('netbs.fichier.config')->getMembreClass());

        return $searcher->render($instance);
    }

    /**
     * @Route("/remove/{id}", name="netbs.fichier.membre.remove")
     */
    public function removeMembreAction($id) {

        if(!$this->isGranted('ROLE_SG'))
            throw $this->createAccessDeniedException("Opération refusée!");

        $config = $this->get('netbs.fichier.config');
        $em = $this->getDoctrine()->getManager();
        $membre = $em->find($config->getMembreClass(), $id);

        $this->get('event_dispatcher')->dispatch(RemoveMembreEvent::NAME, new RemoveMembreEvent($membre, $em));

        $em->remove($membre);
        $em->flush();
        $this->addFlash('success', 'Membre supprimé');
        return $this->redirectToRoute('netbs.core.home.dashboard');
    }
}
