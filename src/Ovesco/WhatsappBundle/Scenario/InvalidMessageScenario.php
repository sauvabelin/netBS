<?php

namespace Ovesco\WhatsappBundle\Scenario;

use Ovesco\WhatsappBundle\Model\Message;
use Ovesco\WhatsappBundle\Model\ScenarioInterface;
use Twilio\TwiML\MessagingResponse;

class InvalidMessageScenario implements ScenarioInterface
{
    public static function getPoids()
    {
        return 0;
    }

    public function elligible(Message $message)
    {
        return empty($message->messageId)
            || strpos($message->from, "whatsapp") === false
            || empty($message->body)
            || empty($message->accountId);
    }

    public function getResponse(Message $message)
    {
        $response = new MessagingResponse();
        $response->message('Requête incorrecte');
        return $response;
    }
}
