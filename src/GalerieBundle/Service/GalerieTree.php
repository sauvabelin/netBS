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

        $result = $this->em->getRepository('GalerieBundle:Media')
            ->createQueryBuilder('m')
            ->where("m.webdavUrl LIKE :dir")
            ->setParameter("dir", $directory->getSearchPath() . "%")
            ->setMaxResults(1)
            ->getQuery()
            ->execute();

        if(is_array($result) && count($result) > 0)
            return $result[0];
    }

    /**
     * @param Directory $directory
     * @param bool $recursive
     * @return Media[]
     */
    public function getMedias(Directory $directory, $recursive = false) {

        $query = $this->em->getRepository('GalerieBundle:Media')
            ->createQueryBuilder('m')
            ->where("m.webdavUrl LIKE :dir")
            ->setParameter("dir", $directory->getSearchPath() . "%");

        if(!$recursive)
            $query->andWhere("(CHAR_LENGTH(m.webdavUrl) - CHAR_LENGTH(REPLACE(m.webdavUrl, '/', ''))) - (CHAR_LENGTH(:path) - CHAR_LENGTH(REPLACE(:path, '/', ''))) = 0")
                ->setParameter('path', $directory->getSearchPath());

        return $query->getQuery()
            ->execute();
    }

    /**
     * @param Directory $directory
     * @param bool $recursive
     * @return Directory[]
     */
    public function getChildren(Directory $directory, $recursive = false) {

        $query = $this->em->getRepository('GalerieBundle:Directory')
            ->createQueryBuilder('d')
            ->where("d.webdavUrl LIKE :dir")
            ->setParameter("dir", $directory->getSearchPath() . "%")
            ->andWhere("d.id != :id")
            ->setParameter("id", $directory->getId());

        if(!$recursive)
            $query->andWhere("(CHAR_LENGTH(d.webdavUrl) - CHAR_LENGTH(REPLACE(d.webdavUrl, '/', ''))) - (CHAR_LENGTH(:path) - CHAR_LENGTH(REPLACE(:path, '/', ''))) = 1")
                ->setParameter('path', $directory->getSearchPath());

        return $query->getQuery()
            ->execute();
    }
}