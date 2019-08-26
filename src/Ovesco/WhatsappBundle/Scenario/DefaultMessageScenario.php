<?php

namespace Ovesco\WhatsappBundle\Scenario;

use Ovesco\WhatsappBundle\Model\Message;
use Ovesco\WhatsappBundle\Model\ScenarioInterface;
use Twilio\TwiML\MessagingResponse;

class DefaultMessageScenario implements ScenarioInterface
{
    public static function getPoids()
    {
        return 99999;
    }

    public function elligible(Message $message)
    {
        return true;
    }

    public function getResponse(Message $message)
    {
        $response = new MessagingResponse();
        $response->message("Désolé mais j'ai pas compris, si t'as besoin d'aide envoie _help_ pour avoir une liste des commandes disponibles");
        return $response;
    }
}
