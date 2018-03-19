<?php

namespace NetBS\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
        dump($request->request->all());
        $helperManager  = $this->get('netbs.core.helper_manager');
        $class          = base64_decode($request->request->get('class'));
        $id             = $request->request->get('id');

        dump($class);
        dump($id);
        $item           = $this->getDoctrine()->getRepository($class)->find($id);

        if(!$item)
            throw $this->createNotFoundException("Object not found");

        $helper         = $helperManager->getFor($class);
        $data           = [
            'content'   => $helper->render($item),
            'title'     => $helper->getRepresentation($item)
        ];

        return new JsonResponse($data);
    }
}
