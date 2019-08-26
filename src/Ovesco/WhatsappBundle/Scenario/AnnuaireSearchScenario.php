<?php

namespace Ovesco\WhatsappBundle\Scenario;

use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\FichierBundle\Select2\MembreProvider;
use Ovesco\WhatsappBundle\Model\Message;
use Ovesco\WhatsappBundle\Model\ScenarioInterface;
use Twilio\TwiML\MessagingResponse;

class AnnuaireSearchScenario implements ScenarioInterface
{
    private $provider;

    public function __construct(MembreProvider $provider)
    {
        $this->provider = $provider;
    }

    public static function getPoids()
    {
        return 100;
    }

    public function elligible(Message $message)
    {
        $messageData = explode(' ', $message->getCleanBody());
        return in_array($messageData[0], ['annuaire', 'book']) && count($messageData) >= 2;
    }

    public function getResponse(Message $message)
    {
        $messageData = explode(' ', $message->getCleanBody());
        $searchTerm = implode(" ", array_slice($messageData, 1));
        $data = $this->provider->search($searchTerm, 5);
        $texte = "";
        foreach ($data as $membre)
            $texte .= $this->membreToString($membre) . "\n";

        $response = new MessagingResponse();
        $response->message($texte);
        return $response;
    }

    private function membreToString(BaseMembre $membre) {

        $texte = "*" . $membre->__toString() . "*\n";
        $tel = $membre->getSendableTelephone();
        $mail = $membre->getSendableEmail();
        $adresse = $membre->getSendableAdresse();

        if ($tel)
            $texte .= "tel: " . $tel->getTelephone() . "\n";
        if ($mail)
            $texte .= "mail: " . $mail->getEmail() . "\n";
        if ($adresse) {
            if ($tel || $mail) $texte .= "\n";
            $texte .= "adresse:\n";
            $texte .= $adresse->getRue() . "\n";
            $texte .= $adresse->getNpa() . " " . $adresse->getLocalite() . "\n";
        }

        return $texte;
    }
}
