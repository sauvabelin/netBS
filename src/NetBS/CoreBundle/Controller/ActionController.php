<?php

namespace NetBS\CoreBundle\Controller;

use NetBS\SecureBundle\Voter\CRUD;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActionController extends Controller
{
    /**
     * @param $itemId
     * @param $itemClass
     * @Route("/app/actions/remove-item/{itemId}/{itemClass}", name="netbs.core.action.remove_item")
     * @return Response
     */
    public function removeItemAction($itemId, $itemClass) {

        $itemClass  = base64_decode($itemClass);
        $em         = $this->get('doctrine.orm.entity_manager');

        $item       = $em->find($itemClass, $itemId);

        if($item) {

            if(!$this->isGranted(CRUD::DELETE, $item))
                throw $this->createAccessDeniedException();

            $em->remove($item);
            $em->flush();

            $this->addFlash("info", "élément supprimé avec succès");
        }

        else{
            $this->addFlash("warning", "Une erreur s'est produite, action interrompue");
        }

        return $this->get('netbs.core.history')->getPreviousRoute();
    }
}