<?php

namespace GalerieBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use GalerieBundle\Entity\Directory;
use GalerieBundle\Entity\Media;
use GalerieBundle\Model\NCNode;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Service\FilterService;

class GalerieMapper
{
    const THUMBNAIL     = "thumbnail";
    const BIGNAIL       = "bignail";

    private $em;

    private $cacheManager;

    private $filter;

    public function __construct(ObjectManager $manager, CacheManager $cacheManager, FilterService $filter)
    {
        $this->em           = $manager;
        $this->cacheManager = $cacheManager;
        $this->filter       = $filter;
    }

    public function map(NCNode $node) {

        $media      = $this->getMedia($node);
        if($media)
            return $this->update($node);

        $this->checkDirectoryTree($node);
        $media      = $node->toMedia();

        $this->em->persist($media);
        $this->em->flush();

        $this->generateCache($media);

        return true;
    }

    public function update(NCNode $node) {

        $media  = $this->getMedia($node);
        $this->removeCache($media);

        if(!$media)
            return $this->map($node);

        $this->checkDirectoryTree($node);
        $media->setFilename($node->getFilename());
        $media->setWebdavUrl($node->getWebdavUrl());
        $this->em->persist($media);
        $this->em->flush();

        $this->generateCache($media);
    }

    public function remove(NCNode $node) {

        $media  = $this->getMedia($node);

        if($media) {

            $this->em->remove($media);
            $this->em->flush();
        }

        $this->removeCache($media);
    }

    private function getMedia(NCNode $node) {

        return $this->em->getRepository('GalerieBundle:Media')
            ->findOneBy(array('etag' => $node->getEtag()));
    }

    private function checkDirectoryTree(NCNode $node) {

        $pathData   = explode('/', $node->getWebdavUrl());
        $path       = "";
        $directory  = null;

        for($i = 0; $i < count($pathData) - 1; $i++) {
            $path .= $pathData[$i] . "/";

            $directory  = $this->em->getRepository('GalerieBundle:Directory')
                ->findOneBy(array('webdavUrl' => $path));

            if(!$directory) {

                $directory  = new Directory();
                $directory->setName($pathData[$i]);
                $directory->setWebdavUrl($path);

                $this->em->persist($directory);
                $this->em->flush();
            }
        }

        return $directory;
    }

    public function removeCache(Media $media) {

        $this->cacheManager->remove($media->getsearchPath());
    }

    public function generateCache(Media $media) {

        $this->filter->getUrlOfFilteredImage($media->getsearchPath(), self::THUMBNAIL);
        $this->filter->getUrlOfFilteredImage($media->getsearchPath(), self::BIGNAIL);
    }
}