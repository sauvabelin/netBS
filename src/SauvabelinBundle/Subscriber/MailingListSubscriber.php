<?php

namespace SauvabelinBundle\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use SauvabelinBundle\Entity\RedirectMailingList;
use SauvabelinBundle\Entity\RuleMailingList;
use SauvabelinBundle\Model\MailingList;
use SauvabelinBundle\Service\ISPConfigManager;

class MailingListSubscriber implements EventSubscriber
{
    private $ISPConfigManager;

    public function __construct(ISPConfigManager $ISPConfigManager)
    {
        $this->ISPConfigManager = $ISPConfigManager;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove
        ];
    }

    public function postPersist(LifecycleEventArgs $args) {

        $list   = $args->getEntity();

        //Ajout de la nouvelle liste
        if($list instanceof RedirectMailingList)
            $this->ISPConfigManager->createMailingList($list->getFromAdresse(), $list->getToAdressesAsArray());

        if($list instanceof RuleMailingList)
            $this->ISPConfigManager->createMailingList($list->getFromAdresse(), []); //Nothing to add as destination yet
    }

    public function postUpdate(LifecycleEventArgs $args) {

        $list   = $args->getEntity();

        if($list instanceof RedirectMailingList)
            $this->ISPConfigManager->updateMailingList($list->getFromAdresse(), $list->getToAdressesAsArray());

        //Do nothing for RuleMailingList, must update on ISPConfig
    }

    public function postRemove(LifecycleEventArgs $args) {

        $list   = $args->getEntity();

        //Remove the mailing list
        if($list instanceof MailingList)
            $this->ISPConfigManager->deleteMailingList($list->getFromAdresse());
    }
}