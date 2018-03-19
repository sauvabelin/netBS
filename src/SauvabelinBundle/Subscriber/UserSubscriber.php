<?php

namespace SauvabelinBundle\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use NetBS\CoreBundle\Service\ParameterManager;
use NetBS\CoreBundle\Utils\StrUtil;
use NetBS\FichierBundle\Mapping\BaseAttribution;
use NetBS\SecureBundle\Mapping\BaseUser;
use NetBS\SecureBundle\Service\UserManager;
use SauvabelinBundle\Entity\BSUser;

class UserSubscriber implements EventSubscriber
{
    private $userManager;

    public function __construct()
    {
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::preUpdate,
            Events::postUpdate,
            Events::postRemove
        ];
    }

    public function postPersist(LifecycleEventArgs $args) {

        return;
        $attribution = $args->getEntity();

        //Create user account if doesnt exist yet
        if($attribution instanceof BaseAttribution) {
            if ($attribution->getFonction()->getPoids() >= 100 && !$this->userManager->findMembreLinkedUser($attribution->getMembre()))
                //$this->createUser($attribution, $args->getEntityManager());


            if($attribution->getFonction()->getPoids() >= 500) {

                $user   = $this->userManager->findMembreLinkedUser($attribution->getMembre());

                if(!$user->getMembre()->getSendableEmail());

            }
        }
    }

    public function preUpdate(PreUpdateEventArgs $args) {

        if($args->getEntity() instanceof BaseUser) {

            dump($args);
        }
    }

    public function postUpdate(LifecycleEventArgs $args) {

        if($args->getEntity() instanceof BaseUser) {

            dump($args);
        }
    }

    public function postRemove(LifecycleEventArgs $args) {

    }

    private function createUser(BaseAttribution $attribution, EntityManager $manager) {

        $membre         = $attribution->getMembre();
        $user           = new BSUser();
        $baseUsername   = str_replace(' ', '.', trim($membre->getPrenom() . "." . $membre->getFamille()->getNom()));
        $password       = StrUtil::randomString();

        $user->setUsername($this->userManager->buildUsername(StrUtil::slugify($baseUsername)));
        $user->setPassword($this->userManager->encodePassword($user, $password));
        $user->setMembre($membre);

        $manager->persist($membre);
        $manager->flush();
    }
}