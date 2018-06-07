<?php

namespace GalerieBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use GalerieBundle\Entity\Directory;
use GalerieBundle\Entity\Media;

class GalerieTree
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em   = $em;
    }

    /**
     * @param Directory $directory
     * @return Media
     */
    public function getThumbnail(Directory $directory) {

        return $this->em->getRepository('GalerieBundle:Media')
            ->createQueryBuilder('m')
            ->where("m.webdavUrl LIKE :dir")
            ->setParameter("dir", $directory->getWebdavUrl() . "%")
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }

    public function getMedias(Directory $directory) {

        return $this->em->getRepository('GalerieBundle:Media')
            ->createQueryBuilder('m')
            ->where("m.webdavUrl LIKE :dir")
            ->setParameter("dir", $directory->getWebdavUrl() . "%")
            ->andWhere("(CHAR_LENGTH(m.webdavUrl) - CHAR_LENGTH(REPLACE(m.webdavUrl, '/', ''))) - (CHAR_LENGTH(:path) - CHAR_LENGTH(REPLACE(:path, '/', ''))) = 0")
            ->setParameter('path', $directory->getWebdavUrl())
            ->getQuery()
            ->execute();
    }

    public function getChildren(Directory $directory) {

        return $this->em->getRepository('GalerieBundle:Directory')
            ->createQueryBuilder('d')
            ->where("d.webdavUrl LIKE :dir")
            ->setParameter("dir", $directory->getWebdavUrl() . "%")
            ->andWhere("d.id != :id")
            ->setParameter("id", $directory->getId())
            ->andWhere("(CHAR_LENGTH(d.webdavUrl) - CHAR_LENGTH(REPLACE(d.webdavUrl, '/', ''))) - (CHAR_LENGTH(:path) - CHAR_LENGTH(REPLACE(:path, '/', ''))) = 1")
            ->setParameter('path', $directory->getWebdavUrl())
            ->getQuery()
            ->execute();
    }
}