<?php

namespace SauvabelinBundle\Subscriber;

use Doctrine\ORM\EntityManager;
use NetBS\SecureBundle\Event\UserPasswordChangeEvent;
use SauvabelinBundle\Entity\BSUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventUserAccountSubscriber implements EventSubscriberInterface
{
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager  = $manager;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserPasswordChangeEvent::NAME => "passwordChanged"
        ];
    }

    /**
     * Appelé lorsque le mot de passe d'un utilisateur a changé depuis sa page de compte
     * @param UserPasswordChangeEvent $event
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function passwordChanged(UserPasswordChangeEvent $event) {

        /** @var BSUser $user */
        $user   = $event->getUser();
        $user->setNewPasswordRequired(false);

        $lastCreatedAccount = $this->manager->getRepository('SauvabelinBundle:LatestCreatedAccount')
            ->findBy(array('user' => $user));

        if(is_array($lastCreatedAccount) && count($lastCreatedAccount) > 0)
            foreach($lastCreatedAccount as $item)
                $this->manager->remove($item);

        $this->manager->persist($user);
        $this->manager->flush();
    }
}