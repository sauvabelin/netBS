<?php

namespace NetBS\FichierBundle\Controller;

use NetBS\CoreBundle\Block\Model\Tab;
use NetBS\CoreBundle\Block\CardBlock;
use NetBS\CoreBundle\Block\TabsCardBlock;
use NetBS\CoreBundle\Block\TemplateBlock;
use NetBS\CoreBundle\Event\RemoveFamilleEvent;
use NetBS\CoreBundle\Event\RemoveMembreEvent;
use NetBS\FichierBundle\Form\FamilleType;
use NetBS\FichierBundle\Mapping\BaseFamille;
use NetBS\SecureBundle\Voter\CRUD;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MembreController
 * @Route("/famille")
 */
class FamilleController extends Controller
{
    protected function fclass() {
        return $this->get('netbs.fichier.config')->getFamilleClass();
    }

    /**
     * @Route("/page/{id}", name="netbs.fichier.famille.page_famille")
     * @param $id
     * @return Response
     */
    public function pageFamilleAction($id) {

        /** @var BaseFamille $famille */
        $famille    = $this->get('doctrine.orm.entity_manager')->find($this->fclass(), $id);

        if(!$famille)
            throw $this->createNotFoundException("Aucune famille trouvée");

        if(!$this->isGranted(CRUD::READ, $famille))
            throw $this->createAccessDeniedException();

        $layout     = $this->get('netbs.core.block.layout');
        $form       = $this->createForm(FamilleType::class, $famille)->createView();

        $config     = $layout::configurator()
            ->addRow()
                ->pushColumn(3)
                    ->addRow()
                        ->pushColumn(12)
                            ->setBlock(CardBlock::class, [
                                'template'  => "@NetBSFichier/famille/presentation.block.twig",
                                'title'     => $famille->__toString(),
                                'params'    => [
                                    'form'  => $form
                                ]
                            ])
                        ->close()
                    ->close()
                    ->addRow()
                        ->pushColumn(12)
                            ->setBlock(TemplateBlock::class, [
                                'template'  => '@NetBSFichier/block/famille_link.block.twig',
                                'params'    => [
                                    'famille' => $famille,
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
                                    'item'      => $famille,
                                    'form'      => $form
                                ]),
                                new Tab($famille, '@NetBSFichier/block/tabs/editable_contact.tab.twig', [
                                    'item'      => $famille,
                                    'form'      => $form
                                ]),
                                new Tab('Membres', '@NetBSFichier/famille/famille_membres.block.twig', [
                                    'famille'   => $famille
                                ]),
                                new Tab('Responsables légaux', "@NetBSFichier/famille/famille_geniteurs.block.twig", [
                                    'famille'   => $famille,
                                    'form'      => $form
                                ])
                            ]])
                        ->close()
                    ->close()
                ->close()
            ->close();

        return $layout->renderResponse('netbs', $config, [
            'title' => $famille->__toString(),
            'item'  => $famille
        ]);
    }


    /**
     * @Route("/remove/{id}", name="netbs.fichier.famille.remove")
     */
    public function removeFamilleAction($id) {

        if(!$this->isGranted('ROLE_SG'))
            throw $this->createAccessDeniedException("Opération refusée!");

        $config = $this->get('netbs.fichier.config');
        $em = $this->getDoctrine()->getManager();
        /** @var BaseFamille $famille */
        $famille = $em->find($config->getFamilleClass(), $id);

        foreach($famille->getMembres() as $membre) {
            $this->get('event_dispatcher')->dispatch(RemoveMembreEvent::NAME, new RemoveMembreEvent($membre, $em));
            $em->remove($membre);
        }

        $this->get('event_dispatcher')->dispatch(RemoveFamilleEvent::NAME, new RemoveFamilleEvent($famille, $em));

        $em->remove($famille);
        $em->flush();
        $this->addFlash('success', 'Famille supprimée');
        return $this->redirectToRoute('netbs.core.home.dashboard');
    }
}
