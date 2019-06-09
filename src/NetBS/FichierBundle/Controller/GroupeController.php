<?php

namespace NetBS\FichierBundle\Controller;

use NetBS\CoreBundle\Block\CardBlock;
use NetBS\CoreBundle\Block\Model\Tab;
use NetBS\CoreBundle\Block\TabsCardBlock;
use NetBS\CoreBundle\Block\TemplateBlock;
use NetBS\CoreBundle\Utils\Modal;
use NetBS\FichierBundle\Form\GroupeType;
use NetBS\FichierBundle\Mapping\BaseGroupe;
use NetBS\SecureBundle\Voter\CRUD;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GroupeController
 */
class GroupeController extends Controller
{
    protected function getGroupeClass() {
        return $this->get('netbs.fichier.config')->getGroupeClass();
    }

    /**
     * @param Request $request
     * @Route("/modal/add", name="netbs.fichier.groupe.modal_add")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('ROLE_CREATE_EVERYWHERE')")
     */
    public function addGroupeModalAction(Request $request) {

        $gclass         = $this->getGroupeClass();
        $groupe         = new $gclass();
        $form           = $this->createForm(GroupeType::class, $groupe);

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()) {

            $em         = $this->get('doctrine.orm.entity_manager');
            $em->persist($form->getData());
            $em->flush();

            $this->addFlash("success", "Groupe {$groupe->getNom()} ajouté!");
            return Modal::refresh();
        }

        return $this->render('@NetBSFichier/generic/add_generic.modal.twig', [
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }

    /**
     * @Route("/groupes", name="netbs.fichier.groupe.page_groupes_hierarchy")
     * @Security("is_granted('ROLE_READ_EVERYWHERE')")
     * @return Response
     */
    public function pageGroupesHierarchyAction() {

        $em         = $this->get('doctrine.orm.default_entity_manager');
        $config     = $this->get('netbs.fichier.config');
        $repo       = $em->getRepository($this->getGroupeClass());

        /** @var BaseGroupe[] $groupes */
        $groupes    = $repo->findAll();
        $types      = $em->getRepository($config->getGroupeTypeClass())->findAll();
        $categories = $em->getRepository($config->getGroupeCategorieClass())->findAll();

        return $this->render('@NetBSFichier/groupe/page_groupes_hierarchy.html.twig', array(
            'groupes'       => $groupes,
            'types'         => $types,
            'categories'    => $categories
        ));
    }

    /**
     * @Route("/groupe/{id}", name="netbs.fichier.groupe.page_groupe")
     * @return Response
     */
    public function pageGroupeAction($id) {

        /** @var BaseGroupe $groupe */
        $class  = $this->getGroupeClass();
        $groupe = $this->get('doctrine.orm.entity_manager')->find($class, $id);

        if(!$groupe)
            throw $this->createNotFoundException();

        if(!$this->isGranted(CRUD::READ, $groupe))
            throw $this->createAccessDeniedException("Vous n'avez pas les accès requis pour consulter ce groupe");

        $layout = $this->get('netbs.core.block.layout');
        $form   = $this->createForm(GroupeType::class, $groupe)->createView();
        $config = $layout::configurator();

        $tabs   = [
            new Tab('Effectifs', '@NetBSFichier/groupe/list_attributions.tab.twig', [
                'groupe'    => $groupe,
                'list'      => 'netbs.fichier.groupe.attributions'
            ])
        ];

        if($groupe->getGroupeType()->getAffichageEffectifs())
            foreach($groupe->getEnfants() as $enfant)
                $tabs[] = new Tab($enfant->getNom(), '@NetBSFichier/groupe/list_attributions.tab.twig', [
                    'groupe'    => $enfant,
                    'list'      => 'netbs.fichier.groupe.attributions'
                ]);

        $config
            ->addRow()
                ->pushColumn(3)
                    ->addRow()
                        ->pushColumn(12)
                            ->setBlock(CardBlock::class, [
                                'template'  => "@NetBSFichier/groupe/presentation.block.twig",
                                'title'     => $groupe->getNom(),
                                'subtitle'  => $groupe->getGroupeType()->getNom() . ' - ' . $groupe->getGroupeType()->getGroupeCategorie()->getNom(),
                                'params'    => [
                                    'form'  => $form
                                ]
                            ])
                        ->close()
                        ->pushColumn(12)
                            ->setBlock(TemplateBlock::class, [
                                'template'  => '@NetBSFichier/groupe/children.block.twig',
                                'params'    => [
                                    'groupe'    => $groupe
                                ]
                            ])
                        ->close()
                        ->pushColumn(12)
                            ->setBlock(TemplateBlock::class, [
                                'template'  => '@NetBSFichier/groupe/groupe_links.block.twig',
                                'params'    => [
                                    'groupe'    => $groupe
                                ]
                            ])
                        ->close()
                    ->close()
                ->close()
                ->pushColumn(9)
                    ->setBlock(TabsCardBlock::class, [
                        'tabs'  => $tabs,
                        'table' => true
                    ])
                ->close()
            ->close()
        ;

        return $layout->renderResponse('netbs', $config, [
            'title' => $groupe->getNom(),
            'item'  => $groupe
        ]);
    }

    /**
     * @param $id
     * @param $type
     * @return Response|\Symfony\Component\HttpFoundation\StreamedResponse
     * @Route("/groupe/export/{type}/{id}", name="netbs.fichier.groupe.export_groupe")
     */
    public function exportGroupeAction($id, $type) {

        $em     = $this->get('doctrine.orm.entity_manager');
        $groupe = $em->find($this->getGroupeClass(), $id);

        if(!$groupe)
            throw $this->createNotFoundException();

        if(!$this->isGranted(CRUD::READ, $groupe))
            throw $this->createAccessDeniedException("Vous n'avez pas les accès requis pour consulter ce groupe");

        if(!in_array($type, ['pdf', 'excel']))
            throw $this->createAccessDeniedException("Type $type is not allowed!");

        $ids        = [$groupe->getId()];
        $exporter   = $type == 'excel'
            ? $this->get('netbs.fichier.exporter.excel_membre')
            : $this->get('netbs.fichier.exporter.pdf_list_groupe');

        $class      = $type == 'excel'
            ? $this->get('netbs.fichier.config')->getMembreClass()
            : $this->getGroupeClass();

        if($type == 'excel') {

            $ids    = [];
            $grps   = array_merge([$groupe], $groupe->getEnfantsRecursive());

            /** @var BaseGroupe $grp */
            foreach($grps as $grp)
                foreach($grp->getActivesAttributions() as $attribution)
                    $ids[] = $attribution->getMembre()->getId();
        }

        return $this->forward('NetBSCoreBundle:Export:generateExportBlob', ['data' => json_encode([
            'itemsClass'    => base64_encode($class),
            'selectedIds'   => $ids,
            'exporterAlias' => $exporter->getAlias()
        ])]);
    }
}
