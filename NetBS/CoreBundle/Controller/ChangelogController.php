<?php

namespace NetBS\CoreBundle\Controller;

use NetBS\CoreBundle\Entity\LoggedChange;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/changelog")
 */
class ChangelogController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/list", name="netbs.core.changelog.list")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('ROLE_SG')")
     */
    public function lookupChangesAction(Request $request) {

        return $this->render('@NetBSCore/changelog/list_changes.html.twig');
    }

    /**
     * @Route("/approve", name="netbs.core.changelog.approve")
     * @param Request $request
     * @return Response
     * @Security("is_granted('ROLE_SG')")
     */
    public function approveChangesAction(Request $request) {

        $data       = json_decode($request->request->get('data'), true);
        $em         = $this->get('doctrine.orm.entity_manager');

        $changes    = $em->createQueryBuilder()->select('c')
            ->from('NetBSCoreBundle:LoggedChange', 'c')
            ->where('c.id IN(:ids)')
            ->setParameter('ids', $data['selectedIds'])
            ->getQuery()
            ->execute();

        /** @var LoggedChange[] $changes */
        foreach($changes as $change)
            $change->setStatus(LoggedChange::APPROVED);

        $em->flush();

        $this->addFlash('success', count($changes) . " modifications approuvées");
        return $this->redirectToRoute('netbs.core.changelog.list');
    }

    /**
     * @param Request $request
     * @internal param LoggedChange $change
     * @Route("ajax/preview", name="netbs.core.changelog.preview_change")
     * @return Response
     * @Security("is_granted('ROLE_SG')")
     */
    public function ajaxPreviewChangeAction(Request $request) {

        $id     = $request->get('logId');
        $em     = $this->get('doctrine.orm.entity_manager');
        $change = $em->find('NetBSCoreBundle:LoggedChange', $id);

        if(!$change)
            throw $this->createNotFoundException();

        return $this->render('@NetBSCore/changelog/diff.ajax.twig', [
            'log'       => $change
        ]);
    }
}
