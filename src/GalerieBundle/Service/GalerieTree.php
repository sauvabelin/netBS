<?php

namespace GalerieBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use GalerieBundle\Entity\Directory;

class GalerieTree
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em   = $em;
    }

    public function getMedias(Directory $directory) {

        return $this->em->getRepository('GalerieBundle:Media')
            ->createQueryBuilder('m')
            ->where("m.webdavUrl LIKE :dir")
            ->setParameter("dir", $directory->getWebdavUrl())
            ->getQuery()
            ->execute();
    }

    public function getChildren(Directory $directory) {

        return $this->em->getRepository('GalerieBundle:Directory')
            ->createQueryBuilder('d')
            ->where("d.webdavUrl LIKE :dir")
            ->setParameter("dir", $directory->getWebdavUrl())
            ->andWhere("d.id != :id")
            ->setParameter("id", $directory->getId())
            ->getQuery()
            ->execute();
    }
}