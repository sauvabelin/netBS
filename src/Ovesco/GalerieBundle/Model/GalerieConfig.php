<?php

namespace Ovesco\GalerieBundle\Model;

class GalerieConfig
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var string
     */
    private $mappedDirectory;

    /**
     * @var string
     */
    private $cacheDirectory;

    /**
     * @var array
     */
    private $imageExtensions;

    /**
     * @var string
     */
    private $descriptionFilename;

    public function __construct($rootDir, $mappedDirectory, $cacheDirectory, $extensions, $descriptionFilename)
    {
        $this->rootDir              = $rootDir;
        $this->mappedDirectory      = $mappedDirectory;
        $this->cacheDirectory       = $cacheDirectory;
        $this->imageExtensions      = $extensions;
        $this->descriptionFilename  = $descriptionFilename;
    }

    /**
     * @return array
     */
    public function getImageExtensions()
    {
        return $this->imageExtensions;
    }

    /**
     * @return string
     */
    public function getDescriptionFilename()
    {
        return $this->descriptionFilename;
    }

    /**
     * @return string
     */
    public function getMappedDirectory()
    {
        return $this->mappedDirectory;
    }

    /**
     * @return string
     */
    public function getCacheDirectory()
    {
        return $this->cacheDirectory;
    }

    public function getFullMappedDirectory() {

        return $this->rootDir . $this->mappedDirectory;
    }

    public function getFullCacheDirectory() {

        return $this->rootDir . $this->cacheDirectory;
    }
}