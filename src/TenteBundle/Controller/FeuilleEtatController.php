<?php

namespace TenteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use TenteBundle\Entity\FeuilleEtat;

/**
 * @package TenteBundle\Controller
 * @Route("/feuille-etat")
 */
class FeuilleEtatController extends Controller
{
    /**
     * @Route("/view/{id}", name="tente.feuille_etat.view")
     */
    public function viewAction(FeuilleEtat $feuilleEtat) {

        return $this->render('@Tente/feuilles/view_feuille.modal.twig', [
            'feuille' => $feuilleEtat
        ]);
    }

    /**
     * @Route("/mark-validation", name="tente.feuille_etat.mark_validated")
     * @param $ids
     * @param $validate
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function markValidated(Request $request) {
        $data = json_decode($request->get('data'), true);
        $em = $this->get('doctrine.orm.default_entity_manager');
        $feuilles = $em->getRepository('TenteBundle:FeuilleEtat')->createQueryBuilder('f')
            ->where('f.id IN (:ids)')
            ->setParameter('ids', $data['selectedIds'])
            ->getQuery()
            ->getResult();
        /** @var FeuilleEtat $feuille */
        foreach ($feuilles as $feuille)
            $feuille->setValidated($data['validated']);
        $em->flush();

        $this->addFlash('success', 'Feuilles marquées comme ' . ($data['validated'] ? 'lues' : 'non-lues'));
        return $this->get('netbs.core.history')->getPreviousRoute(2);
    }
}
