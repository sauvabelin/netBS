<?php

namespace NetBS\CoreBundle\Controller;

use NetBS\SecureBundle\Voter\CRUD;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class Select2Controller extends Controller
{
    const SEARCH_NEEDLE = 'query';

    /**
     * @param Request $request
     * @Route("/netbs/select2/results", name="netbs.core.select2.results")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function resultsAction(Request $request) {


        $class      = $request->get('ajaxClass');
        $nullOption = $request->get('nullOption') === '1';
        $provider   = $this->get('netbs.core.select2_providers_manager')->getProvider(base64_decode($class));
        $search     = $request->get(self::SEARCH_NEEDLE);
        $items      = $provider->search($search);

        $results    = [];
        foreach($items as $item)
            if($this->isGranted(CRUD::READ, $item))
                $results[] = ['id' => $provider->toId($item), 'text' => $provider->toString($item)];

        if($nullOption)
            array_unshift($results, ['id' => '', 'text' => 'Rien']);

        return $this->json(['results' => $results]);
    }
}
