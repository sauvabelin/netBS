<?php

namespace NetBS\CoreBundle\Controller;

use NetBS\CoreBundle\Model\ConfigurableAutomaticInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DynamicListController
 * @Route("/automatic-list")
 */
class AutomaticListController extends Controller
{
    /**
     * @Route("/view/lists", name="netbs.core.automatic_list.view_lists")
     * @Security("is_granted('ROLE_READ_EVERYWHERE')")
     */
    public function viewListsAction() {

        $automatics = $this->get('netbs.core.automatic_lists_manager');

        return $this->render('@NetBSCore/automatics/view_automatics.page.twig', array(
            'models'    => $automatics->getAutomatics()
        ));
    }

    /**
     * @Route("/view/{alias}", name="netbs.core.automatic_list.view_list")
     * @param Request $request
     * @param $alias
     * @Security("is_granted('ROLE_READ_EVERYWHERE')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewListAction(Request $request, $alias) {

        $model  = $this->get('netbs.core.automatic_lists_manager')->getAutomaticByAlias($alias);
        $form   = null;

        if (!$model->isAllowed($this->getUser()))
            throw $this->createAccessDeniedException("Pas autorisé à utiliser cette liste!");

        if($model instanceof ConfigurableAutomaticInterface) {

            $data   = $model->buildDataHolder();
            $form   = $this->createFormBuilder($data);
            $model->buildForm($form);
            $form   = $form->getForm();

            $form->handleRequest($request);

            if($form->isValid() && $form->isSubmitted())
                $model->_setAutomaticData($form->getData());

            $form = $form->createView();
        }

        return $this->render('@NetBSCore/automatics/view_automatic.html.twig', array(
            'model' => $model,
            'form'  => $form
        ));
    }
}
