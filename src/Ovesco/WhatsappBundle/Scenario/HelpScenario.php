<?php

namespace Ovesco\WhatsappBundle\Scenario;

use Ovesco\WhatsappBundle\Model\Message;
use Ovesco\WhatsappBundle\Model\ScenarioInterface;
use Twilio\TwiML\MessagingResponse;

class HelpScenario implements ScenarioInterface
{
    public static function getPoids()
    {
        return 99998;
    }

    public function elligible(Message $message)
    {
        return $message->getCleanBody() === 'help';
    }

    public function getResponse(Message $message)
    {
        $response = new MessagingResponse();
        $response->message("
Bot de la Brigade de Sauvabelin
Il notifie les news importantes et permet de retrouver les coordonnées des membres facilement. Voici la liste des commandes disponibles:\n
*annuaire*
Permet d'obtenir les coordonnées d'un membre, la commande a la forme _annuaire recherche_\n
*news*
Permet de lire les trois dernières news publiées\n");
        return $response;
    }
}
