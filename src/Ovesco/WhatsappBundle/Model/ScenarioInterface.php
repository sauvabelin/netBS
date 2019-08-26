<?php

namespace Ovesco\WhatsappBundle\Model;

use Twilio\TwiML\MessagingResponse;

interface ScenarioInterface
{
    /**
     * @return int
     */
    public static function getPoids();

    /**
     * @param Message $message
     * @return bool
     */
    public function elligible(Message $message);

    /**
     * @param Message $message
     * @return MessagingResponse
     */
    public function getResponse(Message $message);
}
