<?php

namespace NetBS\CoreBundle\Event;

use Doctrine\ORM\EntityManager;
use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\FichierBundle\Service\FichierConfig;
use Symfony\Component\EventDispatcher\Event;

class RemoveMembreEvent extends Event
{
    const NAME = 'netbs.remove.membre';

    private $membre;

    private $manager;

    public function __construct(BaseMembre $membre, EntityManager $manager) {

        $this->membre = $membre;
        $this->manager = $manager;
    }

    public function getMembre()
    {
        return $this->membre;
    }

    /**
     * @return EntityManager
     */
    public function getManager()
    {
        return $this->manager;
    }
}
