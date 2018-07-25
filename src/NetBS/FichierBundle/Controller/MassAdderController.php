<?php

namespace NetBS\FichierBundle\Controller;

use NetBS\CoreBundle\Controller\MassUpdaterController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class MassUpdaterController
 * @Route("/mass")
 */
class MassAdderController extends MassUpdaterController
{
    /**
     * @Route("/adder", name="netbs.fichier.mass.add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("is_granted('ROLE_CREATE_EVERYWHERE')")
     */
    public function dataCreateAction(Request $request) {

        $config = $this->get('netbs.fichier.config');

        if($request->getMethod() !== 'POST') {

            $this->addFlash('warning', "Opération  d'ajout interrompue, veuillez réessayer.");
            return $this->redirectToRoute('netbs.core.home.dashboard');
        }

        $data       = json_decode($request->get('data'), true);
        $items      = [];


        if($request->get('form') === null) {

            $updatedClass   = $data[self::CLASS_KEY];

            if($updatedClass === 'attribution')        $updatedClass = $config->getAttributionClass();
            elseif($updatedClass === 'distinction')    $updatedClass = $config->getObtentionDistinctionClass();
            else throw $this->createAccessDeniedException();

            $ownerClass = base64_decode($data['ownerClass']);
            $ownerIds   = $data['ownerIds'];

            $bridges    = $this->get('netbs.core.bridge_manager');
            $owners     = $this->getMassItems($ownerClass, $ownerIds);
            $membres    = $bridges->convertItems($owners, $config->getMembreClass());

            foreach($membres as $membre) {

                $item = new $updatedClass();
                $item->setMembre($membre);
                $items[] = $item;
            }
        }

        else {
            $updatedClass   = base64_decode($request->get('form')[self::CLASS_KEY]);
        }

        $formData   = [
            'items'         => $items,
            'updatedClass'  => base64_encode($updatedClass)
        ];

        return $this->handleUpdater($request, $formData, $this->get('netbs.core.mass_updater_manager')->getUpdaterForClass($updatedClass));
    }
}