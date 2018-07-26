<?php

namespace GalerieBundle\Controller;

use GalerieBundle\Entity\Directory;
use GalerieBundle\Form\DistantDirectoryUrlType;
use GalerieBundle\Form\UpdateTokenType;
use GalerieBundle\Model\DistantDirectoryUrl;
use GalerieBundle\Model\UpdateToken;
use NetBS\CoreBundle\Utils\Modal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/netBS/galerie")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="netbs.galerie.admin.dashboard")
     * @return Response
     */
    public function dashboardAction()
    {
        $em     = $this->getDoctrine()->getManager();
        return $this->render('@Galerie/dashboard.html.twig', [

            'countDirectories'  => $this->countEntites("GalerieBundle:Directory"),
            'countMedias'       => $this->countEntites("GalerieBundle:Media")
        ]);
    }

    /**
     * @param Directory $directory
     * @Route("/directory/remove/{directory}", name="netbs.galerie.admin.remove_directory")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeDirectoryAction(Directory $directory) {

        $mapper = $this->get('galerie.mapper');
        $mapper->removeDirectory($directory);

        return $this->redirectToRoute('netbs.galerie.admin.dashboard');
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     * @Route("/modal/update-token", name="netbs.galerie.admin.modal_update_token")
     */
    public function updateTokenModalAction(Request $request) {

        $params     = $this->get('netbs.params');
        $oldToken   = $params->getValue('galerie', 'access_token');
        $form       = $this->createForm(UpdateTokenType::class, new UpdateToken($oldToken));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $newToken   = $form->getData()->getToken();
            $params->setValue('galerie', 'access_token', $newToken);

            return new JsonResponse([
                'message'       => [
                    'type'      => 'success',
                    'content'   => "Clé d'accès à la galerie changée, n'oubliez pas de la transmettre aux parents!"
                ]
            ]);
        }

        return $this->render('@Galerie/update_token.modal.twig', [
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     * @Route("/modal/map-distant", name="netbs.galerie.admin.modal_map_distant")
     */
    public function mapDistantModalAction(Request $request) {

        $form       = $this->createForm(DistantDirectoryUrlType::class, new DistantDirectoryUrl());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $url    = $form->getData()->getUrl();

            return Modal::redirect($this->redirectToRoute('netbs.galerie.mapping.map_distant', [
                'directory' => base64_encode($url)
            ]));
        }

        return $this->render('@Galerie/map_distant_directory.modal.twig', [
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }


    private function countEntites($namespace) {

        return $this->get('doctrine.orm.entity_manager')
            ->createQueryBuilder()
            ->select('COUNT(x)')
            ->from($namespace, 'x')
            ->getQuery()
            ->getScalarResult();
    }
}
