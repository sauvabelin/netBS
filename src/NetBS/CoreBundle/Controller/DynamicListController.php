<?php

namespace NetBS\CoreBundle\Controller;

use NetBS\CoreBundle\Entity\DynamicList;
use NetBS\CoreBundle\Form\DynamicListType;
use NetBS\CoreBundle\Utils\Modal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DynamicListController
 * @Route("/dynamic-list")
 */
class DynamicListController extends Controller
{
    /**
     * @Route("/manage/lists", name="netbs.core.dynamics_list.manage_lists")
     */
    public function manageListsAction() {

        $dynamics   = $this->get('netbs.core.dynamic_list_manager');

        return $this->render('@NetBSCore/dynamics/manage_dynamic_lists.html.twig', array(

            'lists'         => $dynamics->getCurrentUserLists()
        ));
    }

    /**
     * @Route("/remove-item/{id}/{itemId}", name="netbs.core.dynamics_list.remove_item")
     * @param DynamicList $list
     * @param $itemId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeElementFromListAction(DynamicList $list, $itemId) {

        $em     = $this->get('doctrine.orm.entity_manager');

        foreach($list->getItems() as $item) {
            if($item->getId() == $itemId) {
                $list->removeItem($item);
                break;
            }
        }

        $em->persist($list);
        $em->flush();

        return $this->redirectToRoute('netbs.core.dynamics_list.manage_list', array('id' => $list->getId()));
    }

    /**
     * @Route("/remove/list/{id}", name="netbs.core.dynamics_list.remove_list")
     * @param DynamicList $list
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeListAction(DynamicList $list) {

        $em     = $this->get('doctrine.orm.entity_manager');

        $em->remove($list);
        $em->flush();

        return $this->redirectToRoute('netbs.core.dynamics_list.manage_lists');
    }

    /**
     * @Route("/manage/{id}", name="netbs.core.dynamics_list.manage_list")
     * @param DynamicList $list
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manageListAction(DynamicList $list) {

        $model      = $this->get('netbs.core.dynamic_list_manager')->getModelForClass($list->getItemsClass());
        $form       = $this->createForm(DynamicListType::class, $list);

        return $this->render('@NetBSCore/dynamics/manage_dynamic_list.html.twig', array(
            'model'     => $model,
            'form'      => $form->createView(),
            'list'      => $list
        ));
    }

    /**
     * @Route("/items/direct-add", name="netbs.core.dynamics_list.items_add")
     * @param Request $request
     * @return DynamicList|null|object
     */
    public function addItemsToList(Request $request) {

        $listId     = $request->get('listId');
        $listItems  = $request->get('selectedIds');
        $itemsClass = base64_decode($request->get('itemsClass'));

        return $this->successResponse($this->performListAddage($listId, $listItems, $itemsClass));
    }

    protected function performListAddage($listId, $listItems, $itemsClass) {

        dump($listId, $listItems, $itemsClass);

        $dynamics   = $this->get('netbs.core.dynamic_list_manager');
        $em         = $this->get('doctrine.orm.entity_manager');

        $list       = $em->getRepository('NetBSCoreBundle:DynamicList')
            ->findOneBy(array(
                'owner' => $this->getUser(),
                'id'    => $listId
            ));

        if($list === null)
            throw $this->createNotFoundException("Aucune liste avec cet identifiant trouvé pour l'utilisateur courant!");

        if(is_array($listItems)) {

            foreach ($listItems as $itemId) {

                $item = $em->getRepository($itemsClass)->find($itemId);

                if($item !== NULL)
                    $dynamics->addItemToList($item, $list);
            }

            $em->persist($list);
            $em->flush();
        }

        return $list;

    }

    /**
     * @param Request $request
     * @Route("/modal/add-list", name="netbs.core.dynamic_list.modal_add")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addListModalAction(Request $request) {

        $dynamics   = $this->get('netbs.core.dynamic_list_manager');
        $list       = new DynamicList();
        $form       = $this->createForm(DynamicListType::class, $list);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $list = $dynamics->saveNewList($form->getData());
            return $this->json([
                'listId' =>$list->getId(),
                'class'  => $list->getItemsClass()
            ], 201);
        }

        return $this->render('@NetBSCore/dynamics/create.modal.twig', [
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }

    protected function successResponse(DynamicList $list) {

        return $this->json([
            'id'    => $list->getId(),
            'name'  => $list->getName(),
            'count' => count($list->getItems())
        ]);
    }
}
