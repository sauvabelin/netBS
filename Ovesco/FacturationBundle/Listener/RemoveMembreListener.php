<?php

namespace Ovesco\FacturationBundle\Listener;

use NetBS\CoreBundle\Event\RemoveFamilleEvent;
use NetBS\CoreBundle\Event\RemoveMembreEvent;
use Ovesco\FacturationBundle\Subscriber\DoctrineDebiteurSubscriber;

class RemoveMembreListener
{
    public function onRemove(RemoveMembreEvent $event) {
        $membre = $event->getMembre();
        $manager = $event->getManager();
        $this->remove($membre, $manager);
    }

    public function onRemoveFamille(RemoveFamilleEvent $event) {
        $famille = $event->getFamille();
        $manager = $event->getManager();
        $this->remove($famille, $manager);
    }

    private function remove($debiteur, $manager) {

        $factures = $manager->getRepository('OvescoFacturationBundle:Facture')
            ->findBy(['debiteurId' => DoctrineDebiteurSubscriber::createId($debiteur)]);

        $creances = $manager->getRepository('OvescoFacturationBundle:Creance')
            ->findBy(['debiteurId' => DoctrineDebiteurSubscriber::createId($debiteur)]);

        foreach($factures as $facture)
            $manager->remove($facture);

        foreach ($creances as $creance)
            $manager->remove($creance);
    }
}
