<?php

namespace Ovesco\WhatsappBundle\Controller;

use Ovesco\WhatsappBundle\Model\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\TwiML\MessagingResponse;

class WhatsappController extends Controller
{
    /**
     * @Route("/inbound", methods={"POST"})
     */
    public function indexAction(Request $request)
    {
        $manager = $this->get('ovesco.whatsapp.service.scenarios_manager');
        $scenarios = $manager->getScenarios();
        try {
            $message = new Message($request);
            foreach ($scenarios as $scenario)
                if ($scenario->elligible($message))
                    return new Response($scenario->getResponse($message)->asXML());

            throw $this->createAccessDeniedException();
        } catch (\Exception $e) {
            $response = new MessagingResponse();
            $response->message($e->getMessage());
            return new Response($response->asXML());
        }
    }
}
