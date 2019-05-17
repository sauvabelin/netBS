<?php

namespace SauvabelinBundle\ApiController;

use NetBS\CoreBundle\Entity\NewsChannel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicNewsController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/api/v1/public/netBS/sauvabelin/news", name="sauvabelin.public_api.public_news")
     */
    public function publicNewsAction(Request $request) {

        $em         = $this->get('doctrine.orm.entity_manager');
        $amount     = $this->getValue($request, 'amount', 5);
        $channel    = $this->getValue($request, 'channel', null);
        $page       = $this->getValue($request, 'page', 0);

        $news   = $em->getRepository('NetBSCoreBundle:News')->createQueryBuilder('n');

        if($channel) {
            $channel = $em->getRepository('NetBSCoreBundle:NewsChannel')->findOneBy(array('nom' => $channel));
            if ($channel) $news->where('n.channel = :channel')->setParameter('channel', $channel);
        }

        $result = $news
            ->setFirstResult(intval($page)*intval($amount))
            ->orderBy('n.createdAt', 'DESC')
            ->setMaxResults($amount)
            ->getQuery()
            ->getResult();

        return new JsonResponse($this->get('serializer')->serialize($result, 'json'), 200, [], true);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/api/v1/public/netBS/sauvabelin/channels", name="sauvabelin.public_api.channels")
     */
    public function channelsAction(Request $request) {
        $em = $this->get('doctrine.orm.entity_manager');
        $channels = $em->getRepository('NetBSCoreBundle:NewsChannel')->findAll();
        $channels = array_map(function(NewsChannel $channel) {
            return $channel->getNom();
        }, $channels);

        return new JsonResponse($channels);
    }

    private function getValue(Request $request, $name, $default) {

        $val = $request->get($name);
        return $val === null ? $default : $val;
    }
}
