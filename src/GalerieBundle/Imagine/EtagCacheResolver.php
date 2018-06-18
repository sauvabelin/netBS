<?php

namespace GalerieBundle\Imagine;

use Doctrine\ORM\EntityManager;
use GalerieBundle\Entity\Media;
use Liip\ImagineBundle\Binary\BinaryInterface;
use Liip\ImagineBundle\Exception\Imagine\Cache\Resolver\NotResolvableException;
use Liip\ImagineBundle\Imagine\Cache\Resolver\ResolverInterface;
use Symfony\Component\Filesystem\Filesystem;

class EtagCacheResolver implements ResolverInterface
{
    private $webRoot;

    private $webpath;

    private $filesystem;

    private $manager;

    public function __construct($webRoot, $webpath, EntityManager $manager, Filesystem $filesystem)
    {
        $prefix             = "/galerie/cache/";
        $this->webRoot      = $webRoot . "/web" . $prefix . "/";
        $this->webpath      = $webpath . "/web" . $prefix . "/";
        $this->manager      = $manager;
        $this->filesystem   = $filesystem;
    }

    /**
     * Checks whether the given path is stored within this Resolver.
     *
     * @param string $path
     * @param string $filter
     * @return bool
     */
    public function isStored($path, $filter)
    {
        return file_exists($this->getFSPath($this->getEtagForPath($path), $filter));
    }

    /**
     * Resolves filtered path for rendering in the browser.
     *
     * @param string $path The path where the original file is expected to be
     * @param string $filter The name of the imagine filter in effect
     *
     * @throws NotResolvableException
     *
     * @return string The absolute URL of the cached image
     */
    public function resolve($path, $filter)
    {
        return $this->webpath . $this->getPath($this->getEtagForPath($path), $filter);
    }

    /**
     * Stores the content of the given binary.
     *
     * @param BinaryInterface $binary The image binary to store
     * @param string $path The path where the original file is expected to be
     * @param string $filter The name of the imagine filter in effect
     */
    public function store(BinaryInterface $binary, $path, $filter)
    {
        $this->filesystem->dumpFile(
            $this->getFSPath($this->getEtagForPath($path), $filter),
            $binary->getContent()
        );
    }

    /**
     * @param string[] $paths The paths where the original files are expected to be
     * @param string[] $filters The imagine filters in effect
     */
    public function remove(array $paths, array $filters)
    {
        foreach($paths as $path)
            foreach($filters as $filter)
                $this->filesystem->remove($this->getFSPath($this->getEtagForPath($path), $filter));
    }

    private function getPath($etag, $filter) {

        return $filter . "/". preg_replace('/[^0-9.]+/', '', $etag) . "/" . $etag;
    }

    private function getFSPath($etag, $filter) {

        return $this->getRootDir() . $this->getPath($etag, $filter);
    }

    private function getRootDir() {

        return $this->webRoot;
    }

    private function getEtagForPath($path) {

        $media = $this->manager->getRepository('GalerieBundle:Media')->findOneBy(array('webdavUrl' => $path));
        return $media instanceof Media ? $media->getEtag() : null;
    }
}