<?php

namespace SauvabelinBundle\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\FichierBundle\Mapping\Personne;
use NetBS\SecureBundle\Voter\CRUD;
use SauvabelinBundle\Entity\BSMembre;

/**
 * Class UserAccountSubscriber
 * @package SauvabelinBundle\Subscriber
 * Gère la création de comptes utilisateur basés sur la création d'attributions
 * Aime pas du tout qu'on lui file des services qui dépendent de l'entity manager
 */
class OldFichierMapperSubscriber implements EventSubscriber
{
    private $adabsId = null;

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $item = $args->getEntity();

        if ($item instanceof BaseMembre)
            $this->mapMembre($item, $args->getEntityManager(), CRUD::CREATE);
    }

    public function postUpdate(LifecycleEventArgs $args) {

        $item = $args->getEntity();

        if ($item instanceof BaseMembre)
            $this->mapMembre($item, $args->getEntityManager(), CRUD::UPDATE);
    }

    private function mapMembre(BSMembre $membre, EntityManager $manager, $type) {

        if($this->adabsId === null)
            $this->adabsId = $manager->getRepository('NetBSCoreBundle:Parameter')->findOneBy(array(
                'namespace' => 'bs',
                'paramKey'  => 'groupe.adabs_id'
            ))->getValue();

        $sexe       = ($membre->getSexe() === Personne::HOMME) ? 'm' : 'f';
        $adresse    = $membre->getSendableAdresse();
        $tel        = $membre->getSendableTelephone();
        $email      = $membre->getSendableEmail();

        $data   = [
            'no_membre'         => $membre->getNumeroBS(),
            'nom'               => strtolower($membre->getFamille()->getNom()),
            'prenom'            => strtolower($membre->getPrenom()),
            'sexe'              => $sexe,
            'date_naissance'    => $membre->getNaissance()->format('d-m-Y')
        ];

        if($type === CRUD::CREATE)
            $data['id_fichier'] = 1;

        if($adresse)
            $data = array_merge($data, [
                'adresse'   => $adresse->getRue(),
                'npa'       => $adresse->getNpa(),
                'ville'     => $adresse->getLocalite()
            ]);

        if($email)
            $data['email'] = $email->getEmail();

        if($tel)
            $data['tel'] = $tel->getTelephone();

        if($membre->getInscription())
            $data['date_inscription_bs'] = $membre->getInscription()->format('d-m-Y');

        foreach($membre->getAttributions() as $attribution) {
            if (intval($attribution->getGroupeId()) === intval($this->adabsId)) {
                $data['date_inscription_adabs'] = $attribution->getDateDebut()->format('d-m-Y');
                $data['id_fichier'] = 2;
            }

            if($attribution->getDateFin() instanceof \DateTime) {
                $data['date_demission_adabs'] = $attribution->getDateDebut()->format('d-m-Y');
                $data['id_fichier'] = 4;
            }
        }

        if($membre->getStatut() === BaseMembre::DECEDE)
            $data['id_fichier'] = 5;

        if($membre->getStatut() === BaseMembre::DESINSCRIT)
            $data['id_fichier'] = 4;
    }
}