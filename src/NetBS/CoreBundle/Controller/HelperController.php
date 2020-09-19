<?php

namespace NetBS\CoreBundle\Controller;

use NetBS\SecureBundle\Voter\CRUD;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class HelperController extends Controller
{
    /**
     * @Route("/helper/get-help", name="netbs.core.helper.get_help")
     * @param Request $request
     * @return JsonResponse
     */
    public function getHelpAction(Request $request)
    {
        $helperManager  = $this->get('netbs.core.helper_manager');
        $class          = base64_decode($request->request->get('class'));
        $id             = $request->request->get('id');
        $item           = $this->getDoctrine()->getRepository($class)->find($id);

        if(!$item)
            throw $this->createNotFoundException("Object not found");

        if(!$this->isGranted(CRUD::READ, $item))
            throw $this->createAccessDeniedException();

        $helper         = $helperManager->getFor($class);
        $data           = [
            'content'   => $helper->render($item),
            'title'     => $helper->getRepresentation($item)
        ];

        return new JsonResponse($data);
    }
}
