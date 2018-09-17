<?php

namespace NetBS\CoreBundle\ApiController;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class ApiNewsController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/news", name="netbs.core.api.get_news")
     */
    public function getDirectoryAction(Request $request) {

        $amount = $this->getValue($request, 'amount', 5);
        $page   = $this->getValue($request, 'page', 0);

        $news   = $this->get('doctrine.orm.entity_manager')->getRepository('NetBSCoreBundle:News')
            ->createQueryBuilder('n')
            ->setMaxResults($amount)
            ->setFirstResult(intval($page)*intval($amount))
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return new JsonResponse($this->get('serializer')->serialize($news, 'json'), 200, [], true);
    }

    private function getValue(Request $request, $name, $default) {

        $val = $request->get($name);
        return $val === null ? $default : $val;
    }
}
