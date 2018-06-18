<?php

namespace GalerieBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use GalerieBundle\Entity\Directory;
use GalerieBundle\Entity\Media;
use GalerieBundle\Exceptions\MappingException;
use GalerieBundle\Model\NCNode;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Service\FilterService;
use NetBS\CoreBundle\Service\ParameterManager;
use Sabre\DAV\Client;
use Sabre\HTTP\ClientHttpException;

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

    private $client;

    private $tree;

    public function __construct(ObjectManager $manager, CacheManager $cacheManager, FilterService $filter, ParameterManager $params, Client $client, GalerieTree $tree)
    {
        $this->em           = $manager;
        $this->cacheManager = $cacheManager;
        $this->filter       = $filter;
        $this->params       = $params;
        $this->client       = $client;
        $this->tree         = $tree;
    }

    /**
     * @param $operation
     * @param NCNode $origin
     * @param NCNode $destination
     * @return bool
     * @throws MappingException
     */
    public function handle($operation, $origin, $destination) {

        if($origin && $origin->getMimetype() === "text/markdown" || $destination && $destination->getMimetype() === "text/markdown")
            return $this->handleDescription($operation, $origin, $destination);

        switch($operation) {
            case self::CREATED:
                return $this->map($origin);
                break;
            case self::RENAMED:
                if($destination->isDirectory())
                    return $this->moveDirectory($destination);
                else {
                    $media = $this->getMedia($destination);
                    if ($media)
                        return $this->move($media, $destination);
                }
                break;

            case self::UPDATED:
                $media  = $this->getMedia($origin);
                if($media)
                    return $this->update($this->getMedia($origin));
                else
                    return $this->map($origin);
                break;
            case self::DELETED:
                if($origin->isDirectory())
                    return $this->removeDirectory($this->checkDirectoryTree($origin, false));
                else
                    return $this->remove($this->getMedia($origin));
                break;
            default:
                break;
        }

        return true;
    }

    private function move(Media $media, NCNode $item) {

        $media->setSearchPath($item->getsearchPath());
        $this->em->persist($media);
        $this->em->flush();

        return true;
    }

    /**
     * @param NCNode $destination
     * @return bool
     * @throws MappingException
     */
    public function moveDirectory($destination) {

        $newPart    = trim($destination->getsearchPath(), "/") . "/";
        $remoteId   = $this->getDirectoryRemoteId($destination->getsearchPath());
        $directory  = $this->em->getRepository('GalerieBundle:Directory')
            ->findOneBy(array('remoteId' => $remoteId));


        if(!$directory)
            throw new MappingException(null, "warning",
                "Erreur lors du déplacement de {$destination->getsearchPath()}");

        foreach($this->tree->getChildren($directory, true) as $child) {

            $child->setSearchPath(str_replace($directory->getSearchPath(), $newPart, $child->getSearchPath()));
            $this->em->persist($child);
        }

        foreach($this->tree->getMedias($directory, true) as $media) {

            $media->setSearchPath(str_replace($directory->getSearchPath(), $newPart, $media->getSearchPath()));
            $this->em->persist($media);
        }

        $directory->setSearchPath($newPart);
        $this->em->persist($directory);
        $this->em->flush();

        $this->checkDirectoryTree($destination, false);
        return true;
    }

    private function validateDescriptionFilename($name, $throw = false) {

        $filename   = $this->params->getValue('galerie', 'description_filename');

        if($name !== $filename) {
            if($throw)
                throw new MappingException(null, "warning",
                "[GALERIE] Vous avez ajouté un fichier markdown $name qui ne s'appelle pas $filename.");
            else
                return false;
        }

        return true;
    }

    /**
     * @param $operation
     * @param NCNode $origin
     * @param NCNode $destination
     * @return bool
     * @throws MappingException
     */
    public function handleDescription($operation, NCNode $origin, $destination) {

        $originDir  = $this->checkDirectoryTree($origin);

        try {
            switch ($operation) {
                case self::CREATED:
                    $this->validateDescriptionFilename($origin->getFilename(), true);
                    $originDir->setDescription($this->request('GET', $origin->getsearchPath()));
                    break;
                case self::DELETED:
                    if ($this->validateDescriptionFilename($origin->getFilename(), false))
                        $originDir->setDescription(null);
                    break;
                case self::UPDATED:
                    if ($this->validateDescriptionFilename($origin->getFilename(), false))
                        $originDir->setDescription($this->request('GET', $origin->getsearchPath()));
                    break;
                case self::RENAMED:
                    if ($destination instanceof NCNode && $this->validateDescriptionFilename($destination->getFilename())) {
                        $destinationDir = $this->checkDirectoryTree($destination);
                        $destinationDir->setDescription($this->request('GET', $destination->getsearchPath()));
                        $this->em->persist($destinationDir);
                    }
                    break;
                default:
                    break;
            }

            $this->em->persist($originDir);
            $this->em->flush();
        }

        catch (\Exception $e) {
            throw new MappingException(null, "danger",
                "[GALERIE] Une erreur est survenue pendant la récupération du fichier de description {$origin->getsearchPath()}");
        }

        return true;
    }

    public function map(NCNode $node) {

        $media = $this->getMedia($node);

        if($media)
            return $this->update($this->getMedia($node));

        $this->checkDirectoryTree($node);
        $media = $node->toMedia();

        if(!$this->validate($media))
            return false;

        $this->em->persist($media);
        $this->em->flush();

        $this->generateCache($media);

        return true;
    }

    public function update(Media $media) {

        if(!$this->validate($media))
            return false;

        $this->removeCache($media);

        $this->checkDirectoryTree($media);
        $media->setFilename($media->getFilename());
        $media->setSearchPath($media->getSearchPath());
        $this->em->persist($media);
        $this->em->flush();

        $this->generateCache($media);
    }

    /**
     * @param Media $media
     * @return bool
     */
    public function remove(Media $media) {

        $this->removeCache($media);
        $this->em->remove($media);
        $this->em->flush();

        return true;
    }

    private function removeDirectory(Directory $directory) {

        $medias         = $this->tree->getMedias($directory, true);
        $directories    = $this->tree->getChildren($directory, true);

        foreach($medias as $media)
            $this->remove($media);

        foreach($directories as $directory)
            $this->em->remove($directory);

        $this->em->flush();

        return true;
    }

    /**
     * @param Media $media
     */
    public function removeCache(Media $media) {

        $this->cacheManager->remove($media->getSearchPath());
    }

    public function generateCache(Media $media) {

        try {
            $this->filter->getUrlOfFilteredImage($media->getsearchPath(), self::THUMBNAIL);
            $this->filter->getUrlOfFilteredImage($media->getsearchPath(), self::BIGNAIL);
        } catch (NotLoadableException $e) {
            $this->em->remove($media);
            $this->em->flush();
        }
    }

    /**
     * @param Media $media
     * @return bool
     * @throws MappingException
     */
    private function validate(Media $media) {

        $types  = explode(',', $this->params->getValue('galerie', 'mime_types'));

        if(!in_array($media->getMimetype(), $types))
            throw new MappingException(null, "warning",
                "[GALERIE] Le fichier {$media->getFilename()} n'est pas supporté");

        if($media->getSize() > intval($this->params->getValue('galerie', 'max_size')))
            throw new MappingException(null, "warning",
                "[GALERIE] Le fichier {$media->getsearchPath()} que vous avez voulu ajouter à la galerie est trop grand");

        return true;
    }

    private function getMedia(NCNode $node) {

        return $this->em->getRepository('GalerieBundle:Media')
            ->findOneBy(array('etag' => $node->getEtag()));
    }

    /**
     * @param NCNode|Media $node
     * @param bool $bound
     * @return Directory|null|object
     * @throws MappingException
     */
    private function checkDirectoryTree($node, $bound = true) {

        $pathData   = explode('/', $node->getsearchPath());
        $path       = "";
        $directory  = null;
        $bound      = $bound ? 1 : 0;

        for($i = 0; $i < count($pathData) - $bound; $i++) {

            $path  .= $pathData[$i] . "/";

            $directory  = $this->em->getRepository('GalerieBundle:Directory')
                ->findOneBy(array('webdavUrl' => $path));

            if(!$directory) {

                $remoteId   = $this->getDirectoryRemoteId($path);
                $directory  = new Directory();

                if($remoteId === null)
                    throw new MappingException(null, "critical",
                        "[GALERIE] Une erreur système est survenue avec le dossier $path!");

                $directory->setName($pathData[$i]);
                $directory->setSearchPath($path);
                $directory->setRemoteId($remoteId);
                $this->em->persist($directory);
                $this->em->flush();
            }
        }

        return $directory;
    }

    private function encodePath($path) {

        $realPath   = "";
        foreach(explode("/", $path) as $segment)
            $realPath .= rawurlencode($segment) . "/";

        return trim($realPath, "/") . "/";
    }

    private function decodePath($path) {

        $realPath   = "";
        foreach(explode("/", $path) as $segment)
            $realPath .= rawurldecode($segment) . "/";

        return trim($realPath, "/") . "/";
    }

    private function request($type, $path) {

        return $this->client->request($type, $this->encodePath($path));
    }

    private function getDirectoryRemoteId($path) {

        $key = "{http://owncloud.org/ns}fileid";

        try {

            $id = $this->client->propFind($this->encodePath($path), [
                $key
            ]);

            return $id[$key];

        } catch (ClientHttpException $e) {
            return null;
        }

        /*
        $data   = $this->client->request("PROPFIND", $this->encodePath($path));

        if($data['statusCode'] <= 200 || $data['statusCode'] > 299)
            throw new MappingException(null, "critical",
                "[GALERIE] Une erreur de mapping a eu lieu avec $path, dites le à l'administrateur");

        $dom    = new \DOMDocument();

        $dom->loadXML($data['body']);

        $items  = $dom->getElementsByTagName("response");

        foreach($items as $item) {

            $responsePath   = $item->getElementsByTagName("href")->item(0)->textContent;

            if(substr($this->decodePath($responsePath), strpos($this->decodePath($responsePath), $path)) === $path) {

                $etag = str_replace('"', "", $item->getElementsByTagName('getetag')->item(0)->textContent);
                return $etag;
            }
        }
        */
    }
}
