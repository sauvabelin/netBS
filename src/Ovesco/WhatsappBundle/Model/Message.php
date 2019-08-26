<?php

namespace Ovesco\WhatsappBundle\Model;

use Symfony\Component\HttpFoundation\Request;

class Message
{
    public $accountId;

    public $body;

    public $from;

    public $messageId;

    public function __construct(Request $request)
    {
        $this->accountId    = $request->get('AccountSid');
        $this->body         = $request->get('Body');
        $this->from         = $request->get('From');
        $this->messageId    = $request->get('MessageSid');
    }

    public function getCleanBody() {
        return trim(strtolower($this->body));
    }
}
