<?php

namespace Ovesco\FacturationBundle\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use NetBS\FichierBundle\Mapping\BaseFamille;
use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\FichierBundle\Service\FichierConfig;
use Ovesco\FacturationBundle\Entity\Creance;
use Ovesco\FacturationBundle\Entity\Facture;

class DoctrineDebiteurSubscriber implements EventSubscriber
{
    const MEMBRE    = 'membre';
    const FAMILLE   = 'famille';

    private $config;

    public function __construct(FichierConfig $config)
    {
        $this->config   = $config;
    }

    public function getSubscribedEvents()
    {
        return [
            'postLoad'
        ];
    }

    public function postLoad(LifecycleEventArgs $args) {

        $item       = $args->getEntity();

        if(!$item instanceof Facture && !$item instanceof Creance)
            return;

        $data       = explode(':', $item->_getDebiteurId());
        $class      = $data[0] === self::MEMBRE
            ? $this->config->getMembreClass()
            : $this->config->getFamilleClass();

        $debiteur   = $args->getEntityManager()->find($class, $data[1]);
        $item->setDebiteur($debiteur);
    }

    /**
     * @param BaseMembre|BaseFamille $debiteur
     *
     * @return string
     */
    public static function createId($debiteur) {

        return ($debiteur instanceof BaseMembre ? self::MEMBRE : self::FAMILLE) . ":" . $debiteur->getId();
    }
}