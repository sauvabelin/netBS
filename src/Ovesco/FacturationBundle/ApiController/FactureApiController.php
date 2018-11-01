<?php

namespace Ovesco\FacturationBundle\ApiController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CreanceApiController
 * @package Ovesco\FacturationBundle\ApiController
 * @Route("/facture")
 */
class FactureApiController extends BaseApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("s", name="ovesco.facturation.api.get_factures", methods={"GET"})
     */
    public function getFactures(Request $request) {

        $em = $this->get('doctrine.orm.default_entity_manager');
        $query = $em->createQueryBuilder()->select('x')
            ->from('OvescoFacturationBundle:Facture', 'x')
            ->join('x.compteToUse', 'compte')
            ->leftJoin('x.creances', 'creances')
            ->leftJoin('x.rappels', 'rappels');

        if(($statut = $request->get('statut')) !== null)
            $query->andWhere('x.statut = :statut')->setParameter('statut', $statut);

        if(($titre = $request->get('titre')) !== null)
            $query->andWhere('creances.titre LIKE :titre')->setParameter('titre', $titre);

        if(($date = $request->get('date')) !== null)
            $query->andWhere('x.date = :date')->setParameter('date', new \DateTime($date));

        if(($rappels = $request->get('rappels')) !== null)
            $query->groupBy('x')
                ->andHaving('COUNT(rappels) = :rappels')->setParameter('rappels', $rappels);

        return $this->renderJson($request, $query, ['groups' => [
            'default', 'facture_with_creances', 'facture_with_paiements']]);
    }
}
