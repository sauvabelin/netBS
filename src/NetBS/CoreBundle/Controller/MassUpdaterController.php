<?php

namespace NetBS\CoreBundle\Controller;

use NetBS\CoreBundle\Model\BaseMassUpdater;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class MassUpdaterController
 * @Route("/mass-updater")
 */
class MassUpdaterController extends Controller
{
    const FORM_DATA   = 'data';
    const HOLDER_KEY  = 'holderClass';
    const CLASS_KEY   = 'updatedClass';
    const IDS_KEY     = 'updatedIds';

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/update-data", name="netbs.core.mass_updater.data_update")
     * @Security("is_granted('ROLE_SG')")
     */
    public function dataUpdateAction(Request $request) {

        if($request->getMethod() !== 'POST') {

            $this->addFlash('warning', "Opération de modification interrompue, veuillez réessayer.");
            return $this->redirectToRoute('netbs.core.home.dashboard');
        }

        $class      = null;
        $ids        = null;

        if($request->get('form') === null) {

            $data   = json_decode($request->get(self::FORM_DATA), true);
            $class  = $data[self::CLASS_KEY];
            $ids    = $data[self::IDS_KEY];
        }

        else {

            $data   = $request->get('form');
            $class  = $data[self::CLASS_KEY];
            $ids    = isset($data['ids']) ? json_decode($data['ids']) : [];
        }

        $mass           = $this->get('netbs.core.mass_updater_manager');
        $updater        = $mass->getUpdaterForClass(base64_decode($class));
        $items          = $this->getMassItems(base64_decode($class), $ids);

        $data           = [
            'items'         => $items,
            'updatedClass'  => $data[self::CLASS_KEY],
            'ids'           => json_encode($ids)
        ];

        return $this->handleUpdater($request, $data, $updater);
    }

    /**
     * @param Request $request
     * @param array $data
     * @param BaseMassUpdater $updater
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('ROLE_SG')")
     */
    protected function handleUpdater(Request $request, array $data, BaseMassUpdater $updater) {

        $genericForm    = $this->createForm($updater->getItemForm());

        /** @var Form $massForm */
        $massForm       = $this->createFormBuilder($data)
            ->add('items', CollectionType::class, array(
                'allow_add'     => $updater->allowAdd(),
                'allow_delete'  => $updater->allowDelete(),
                'entry_type'    => $updater->getItemForm()
            ))
            ->add('updatedClass', HiddenType::class)
            ->add('ids', HiddenType::class)
            ->getForm();

        $massForm->handleRequest($request);

        if($massForm->isSubmitted() && $massForm->isValid()) {

            $em     = $this->get('doctrine.orm.entity_manager');
            $items  = $massForm->getData()['items'];

            foreach($items as $item)
                $em->persist($item);

            $em->flush();

            $this->addFlash('success', "Modifications enregistrées pour " . count($items) . " éléments");
            return $this->get('netbs.core.history')->getPreviousRoute(3);
        }

        return $this->render('@NetBSCore/updater/updater.html.twig', array(
            'form'      => $massForm->createView(),
            'generic'   => $genericForm->createView()
        ));
    }

    /**
     * @param $class
     * @param array $ids
     * @return array
     */
    public function getMassItems($class, array $ids) {

        $em         = $this->get('doctrine.orm.entity_manager');

        $items      = $em->createQueryBuilder()
            ->select('x')
            ->from($class, 'x')
            ->where('x.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();

        return $items;
    }
}