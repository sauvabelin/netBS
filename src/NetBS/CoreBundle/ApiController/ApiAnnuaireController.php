<?php

namespace NetBS\CoreBundle\ApiController;

use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\SecureBundle\Mapping\BaseUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class ApiAnnuaireController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/annuaire", name="netbs.core.api.get_annuaire")
     */
    public function getDirectoryAction(Request $request) {

        $query  = $this->get('doctrine.orm.entity_manager')->getRepository($this->get('netbs.secure.config')->getUserClass())
            ->createQueryBuilder('u');

        $users = $query
            ->where($query->expr()->isNotNull('u.membre'))
            ->join("u.membre", "m")
            ->orderBy('m.nom')
            ->andWhere('m.statut = :inscrit')
            ->setParameter('inscrit', BaseMembre::INSCRIT)
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();

        $result = [];

        /** @var BaseUser $user */
        foreach($users as $user) {
            if($user->getMembre()) {

                $membre = $user->getMembre();
                $attribution = $membre->getActiveAttribution();
                $data = [];
                $data["nom"] = $membre->getFullName();

                if ($membre->getSendableEmail())
                    $data['email'] = $membre->getSendableEmail()->getEmail();

                if ($membre->getSendableTelephone())
                    $data['telephone'] = $membre->getSendableTelephone()->getTelephone();

                if ($adresse = $membre->getSendableAdresse()) {
                    $data['adresse'] = [
                        'rue' => $adresse->getRue(),
                        'npa' => $adresse->getNpa(),
                        'localite' => $adresse->getLocalite()
                    ];
                }

                if ($attribution) {
                    $data['groupe'] = $attribution->getGroupe()->getNom();
                    $data['fonction'] = $attribution->getFonction()->getNom();
                }

                $result[] = $data;
            }
        }

        return new JsonResponse($this->get('serializer')->serialize($result, 'json'), 200, [], true);
    }
}
