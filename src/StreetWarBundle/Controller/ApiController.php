<?php

namespace StreetWarBundle\Controller;

use SauvabelinBundle\Entity\BSUser;
use StreetWarBundle\Model\Participant;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends Controller
{
    /**
     * @Route("/participants")
     */
    public function participantsAction()
    {
        $participants = array_map(function(Participant $participant) {
            $participant->cible = null;
            return $participant;
        }, $this->getParticipants());

        return new JsonResponse($participants);
    }

    /**
     * @Route("/cible-info")
     */
    public function cibleAction() {

        $participant = $this->getCurrentParticipant();
        $repo = $this->get('doctrine.orm.entity_manager')->getRepository('SauvabelinBundle:BSUser');

        /** @var BSUser $cible */
        $cible = $repo->findOneBy(['username' => $participant->cible]);
        $membre = $cible->getMembre();

        $adresse = $membre->getSendableAdresse();
        $telephone = $membre->getSendableTelephone();
        $email = $membre->getSendableEmail();
        $attributions = $membre->getActivesAttributions();
        $attrs = [];

        foreach ($attributions as $a) {
            $attrs[] = [
                'groupe' => $a->getGroupe()->getNom(),
                'fonction' => $a->getFonction()->getNom(),
            ];
        }

        return new JsonResponse([
            'username' => $cible->getUsername(),
            'nom' => $membre->__toString(),
            'attributions' => $attrs,
            'adresse' => $adresse ? $adresse->__toString() : null,
            'telephone' => $telephone ? $telephone->__toString() : null,
            'email' => $email ? $email->__toString() : null,
        ]);
    }

    /**
     * @Route("/cible-photo")
     * @return BinaryFileResponse
     */
    public function ciblePhoto() {

        $participant = $this->getCurrentParticipant();
        $path = __DIR__ . "/../../../../stammbox/stammbox-data/Activités/Streetwar/photos/" . $participant->cible . ".jpg";
        if (file_exists($path))
            return new BinaryFileResponse($path);
        else return new BinaryFileResponse(__DIR__ . "/../Resources/assets/ayy.jpg");
    }

    /**
     * @return Participant
     */
    private function getCurrentParticipant() {

        $username = $this->getUser()->getUsername();
        return array_values(array_filter($this->getParticipants(), function($p) use ($username) { return $p->user === $username; }))[0];
    }

    private function getParticipants() {

        $content = file_get_contents(__DIR__ . "/../../../../stammbox/stammbox-data/Activités/Streetwar/participants.txt");
        $content = explode(PHP_EOL, $content);
        return array_map(function($str) {
            $data = array_map(function($s) { return trim($s); }, explode(':', $str));
            return new Participant($data);
        }, $content);
    }
}
