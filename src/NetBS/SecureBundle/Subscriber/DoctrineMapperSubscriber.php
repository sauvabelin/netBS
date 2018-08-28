<?php

namespace NetBS\SecureBundle\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use NetBS\FichierBundle\Service\FichierConfig;
use NetBS\SecureBundle\Service\SecureConfig;

class DoctrineMapperSubscriber implements EventSubscriber
{
    protected $fichierConfig;

    protected $secureConfig;

    public function __construct(FichierConfig $fichierConfig, SecureConfig $secureConfig)
    {
        $this->fichierConfig    = $fichierConfig;
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

            case $this->secureConfig->getRoleClass():
                $this->mapRole($eventArgs);
                break;

            case $this->secureConfig->getUserClass():
                $this->mapUser($eventArgs);
                break;
            default:
                return;
        }
    }

    protected function mapUser(LoadClassMetadataEventArgs $eventArgs) {

        $eventArgs->getClassMetadata()->mapOneToOne([
            'fieldName'     => 'membre',
            'fetch'         => 'EAGER',
            'targetEntity'  => $this->fichierConfig->getMembreClass()
        ]);

        $eventArgs->getClassMetadata()->mapManyToMany([
            'fieldName'     => 'roles',
            'fetch'         => 'EAGER',
            'targetEntity'  => $this->secureConfig->getRoleClass()
        ]);

        $eventArgs->getClassMetadata()->table['uniqueConstraints'][] = [
            'name'      => 'unique_target_member',
            'columns'   => ['membre_id']
        ];
    }

    protected function mapRole(LoadClassMetadataEventArgs $eventArgs) {

        $eventArgs->getClassMetadata()->mapOneToMany([
            'fieldName'     => 'children',
            'targetEntity'  => $this->secureConfig->getRoleClass(),
            'mappedBy'      => 'parent'
        ]);

        $eventArgs->getClassMetadata()->mapManyToOne([
            'fieldName'     => 'parent',
            'targetEntity'  => $this->secureConfig->getRoleClass(),
            'inversedBy'    => 'children'
        ]);
    }
}