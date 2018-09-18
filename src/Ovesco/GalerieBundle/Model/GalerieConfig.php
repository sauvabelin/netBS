<?php

namespace Ovesco\GalerieBundle\Model;

class GalerieConfig
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $imageExtensions;

    /**
     * @var string
     */
    private $descriptionFilename;

    public function __construct($path, $extensions, $descriptionFilename)
    {
        $this->path                 = $path;
        $this->imageExtensions      = $extensions;
        $this->descriptionFilename  = $descriptionFilename;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return trim($this->path, '/');
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
}