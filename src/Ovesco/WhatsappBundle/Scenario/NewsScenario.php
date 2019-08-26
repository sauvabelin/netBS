<?php

namespace Ovesco\WhatsappBundle\Scenario;

use Doctrine\ORM\EntityManager;
use Ovesco\WhatsappBundle\Model\Message;
use Ovesco\WhatsappBundle\Model\ScenarioInterface;
use Twilio\TwiML\MessagingResponse;

class NewsScenario implements ScenarioInterface
{
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public static function getPoids()
    {
        return 200;
    }

    public function elligible(Message $message)
    {
        return $message->getCleanBody() === 'news';
    }

    public function getResponse(Message $message)
    {
        $response = new MessagingResponse();
        $user = $this->manager->getRepository('OvescoWhatsappBundle:WhatsappAccount')->findOneBy(['accountId' => $message->accountId])->getUser();
        $news = $this->manager->getRepository('NetBSCoreBundle:News')->findForUser($user, 3);
        $texte = "";
        foreach ($news as $item) {
            $texte .= "*" . $item->getTitre() . "*\n";
            $texte .= "par _" . $item->getUser()->__toString() . "_ le " . $item->getCreatedAt()->format('d.m.Y') . " dans " . $item->getChannel()->getNom() . "\n";
            $texte .= $item->getContenu() . "\n\n";
        }

        $response->message(strip_tags($texte));
        return $response;
    }
}
