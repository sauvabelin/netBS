<?php

namespace Ovesco\WhatsappBundle\Scenario;

use Doctrine\ORM\EntityManager;
use NetBS\SecureBundle\Service\SecureConfig;
use Ovesco\WhatsappBundle\Entity\WhatsappAccount;
use Ovesco\WhatsappBundle\Model\Message;
use Ovesco\WhatsappBundle\Model\ScenarioInterface;
use Ovesco\WhatsappBundle\Service\MessageService;
use Ovesco\WhatsappBundle\Util\UserSlug;
use Twilio\TwiML\MessagingResponse;

class LoginScenario implements ScenarioInterface
{
    private $manager;

    private $ms;

    private $config;

    /**
     * InvalidMessageScenario constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager, MessageService $messageService, SecureConfig $config)
    {
        $this->manager = $manager;
        $this->ms = $messageService;
        $this->config = $config;
    }

    public static function getPoids()
    {
        return 1;
    }

    public function elligible(Message $message)
    {
        $account = $this->manager->getRepository('OvescoWhatsappBundle:WhatsappAccount')->findOneBy(['accountId' => $message->accountId]);

        return $account === null;
    }

    public function getResponse(Message $message)
    {
        $response = new MessagingResponse();

        $bodyData = explode(' ', $message->getCleanBody());
        if ($bodyData[0] === 'login') {

            $usernames = $this->manager->createQueryBuilder()
                ->select('u.username')
                ->from($this->config->getUserClass(), 'u')
                ->getQuery()
                ->getArrayResult();

            $usernames = array_map(function($i) { return $i['username']; }, $usernames);
            foreach($usernames as $username) {

                // On a notre user, on le log
                if (UserSlug::getSlug($username) === $bodyData[1]) {
                    $user = $this->manager->getRepository($this->config->getUserClass())->findOneBy(['username' => $username]);
                    $account = new WhatsappAccount();
                    $account->setUser($user)
                        ->setAccountId($message->accountId)
                        ->setFrom($message->from);
                    $this->manager->persist($account);
                    $this->manager->flush();
                    $response->message("Vous êtes désormais connecté en tant que " . $user->__toString() . "! Envoyez _help_ pour savoir quelles commandes sont disponibles.");
                    return $response;
                }
            }

            $response->message("Aucun utilisateur trouvé pour cette clé, vérifiez que vous l'avez correctement écrite!");
            return $response;
        }

        $response->message("Vous n'êtes pas encore connecté. Pour cela, tapez _login_ suivi de votre clé que vous trouverez sur le fichier.");
        return $response;
    }
}
