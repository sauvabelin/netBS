<?php

namespace Ovesco\WhatsappBundle\Service;

use Doctrine\ORM\EntityManager;
use Ovesco\WhatsappBundle\Entity\InboundMessage;

class MessageService
{
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $accountSid
     * @return InboundMessage[]
     */
    public function getMessageHistory($accountSid) {

        return $this->manager->getRepository('OvescoWhatsappBundle:InboundMessage')
            ->createQueryBuilder('m')
            ->where('m.accountId = :id')
            ->setParameter('id', $accountSid)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
