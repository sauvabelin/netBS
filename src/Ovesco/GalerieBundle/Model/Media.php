<?php

namespace Ovesco\GalerieBundle\Model;

class Media
{
    private $path;

    private $config;

    public function __construct($path, GalerieConfig $config)
    {
        $this->path     = $path;
        $this->config   = $config;
    }

    public function getName() {

        $segments   = explode('/', $this->path);

        return end($segments);
    }

    public function getSize() {

        return filesize($this->path);
    }

    public function getTimestamp() {

        return filectime($this->path);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getCachePath() {

        return "/" . trim(str_replace($this->config->getPath(), '', $this->path), '/');
    }
}