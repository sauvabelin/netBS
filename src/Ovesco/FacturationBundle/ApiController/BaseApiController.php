<?php

namespace Ovesco\FacturationBundle\ApiController;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BaseApiController extends Controller
{
    protected function renderJson(Request $request, QueryBuilder $query, array $context = []) {

        $maxPerPage = (($param = $request->get('max_results')) === null) ? 10 : $param;
        $currentPage = (($param = $request->get('page')) === null) ? 1 : $param;
        $sort = (($param = $request->get('sort')) === null) ? null : $param;
        $sortDirection = (($param = $request->get('sort_direction')) === null) ? null : $param;

        if($sort) $query->orderBy('x.' . $sort, $sortDirection);
        $adapter = new DoctrineORMAdapter($query, function (QueryBuilder $queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT p.id) AS total_results')
                ->setMaxResults(1);
        });

        $pagerFanta = new Pagerfanta($adapter);
        $pagerFanta->setMaxPerPage($maxPerPage);
        $pagerFanta->setCurrentPage($currentPage);

        $result = iterator_to_array($pagerFanta->getCurrentPageResults());

        return new JsonResponse($this->get('serializer')->serialize([
            'results'       => $result,
            'current_page'  => $currentPage,
            'total_pages'   => $pagerFanta->getNbPages(),
        ], 'json', $context), 200, [], true);
    }
}