<?php

namespace Ovesco\WhatsappBundle\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use NetBS\SecureBundle\Service\SecureConfig;
use Ovesco\WhatsappBundle\Entity\WhatsappAccount;

class DoctrineMapperSubscriber implements EventSubscriber
{
    protected $secureConfig;

    public function __construct(SecureConfig $secureConfig)
    {
        $this->secureConfig     = $secureConfig;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs) {

        switch($eventArgs->getClassMetadata()->getName()) {

            case WhatsappAccount::class:
                $this->mapAccount($eventArgs);
                break;
            default:
                return;
        }
    }

    protected function mapAccount(LoadClassMetadataEventArgs $eventArgs) {

        $eventArgs->getClassMetadata()->mapManyToOne([
            'fieldName'     => 'user',
            'fetch'         => 'EAGER',
            'targetEntity'  => $this->secureConfig->getUserClass()
        ]);
    }
}
