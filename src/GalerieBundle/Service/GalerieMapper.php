<?php

namespace GalerieBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use GalerieBundle\Entity\Directory;
use GalerieBundle\Entity\Media;
use GalerieBundle\Model\NCNode;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Service\FilterService;
use NetBS\CoreBundle\Service\ParameterManager;
use Sabre\DAV\Client;

class GalerieMapper
{
    const CREATED       = "CREATED";
    const UPDATED       = "UPDATED";
    const COPIED        = "COPIED";
    const RENAMED       = "RENAMED";
    const DELETED       = "DELETED";

    const THUMBNAIL     = "thumbnail";
    const BIGNAIL       = "bignail";

    private $em;

    private $cacheManager;

    private $filter;

    private $params;

    private $webdavClient;

    public function __construct(ObjectManager $manager, CacheManager $cacheManager, FilterService $filter, ParameterManager $params, Client $webdavClient)
    {
        $this->em           = $manager;
        $this->cacheManager = $cacheManager;
        $this->filter       = $filter;
        $this->params       = $params;
        $this->webdavClient = $webdavClient;
    }

    public function handle($operation, NCNode $node) {

        if($node->getFilename() === $this->params->getValue('galerie', 'description_filename'))
            return $this->handleDescription($node);

        switch($operation) {
            case self::COPIED:
            case self::CREATED:
                $this->map($node);
                break;
            case self::RENAMED:
            case self::UPDATED:
                $this->update($node);
                break;
            case self::DELETED:
                $this->remove($node);
                break;
            default:
                break;
        }
    }

    public function handleDescription(NCNode $node) {

        $directory  = $this->checkDirectoryTree($node);
        $response   = $this->webdavClient->request("GET", $node->getsearchPath());

        if($response['statusCode'] != 200)
            return false;

        $directory->setDescription($response['body']);
        $this->em->persist($directory);
        $this->em->flush();

        return true;
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

    public function removeCache(Media $media) {

        $this->cacheManager->remove($media->getsearchPath());
    }

    public function generateCache(Media $media) {

        $this->filter->getUrlOfFilteredImage($media->getsearchPath(), self::THUMBNAIL);
        $this->filter->getUrlOfFilteredImage($media->getsearchPath(), self::BIGNAIL);
    }

    private function validate(NCNode $node) {

        $types  = explode(',', $this->params->getValue('galerie', 'mime_types'));

        if(!in_array($node->getMimetype(), $types))
            return false;

        if($node->getSize() > intval($this->params->getValue('galerie', 'max_size')))
            return false;

        return true;
    }

    private function getMedia(NCNode $node) {

        return $this->em->getRepository('GalerieBundle:Media')
            ->findOneBy(array('etag' => $node->getEtag()));
    }

    private function checkDirectoryTree(NCNode $node, $bound = true) {

        $pathData   = explode('/', $node->getWebdavUrl());
        $path       = "";
        $directory  = null;
        $bound      = $bound ? 1 : 0;

        for($i = 0; $i < count($pathData) - $bound; $i++) {
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
}