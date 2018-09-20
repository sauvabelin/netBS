<?php

namespace Ovesco\GalerieBundle\Model;

class Directory
{
    private $path;

    private $config;

    private $medias = null;

    public function __construct($path, GalerieConfig $config)
    {
        $this->path     = $path;
        $this->config   = $config;
    }

    /**
     * @return Directory[]
     */
    public function getChildren() {

        $dirnames   = array_filter(glob($this->path . '/*'), 'is_dir');

        return array_values(array_map(function($name) {
            return new Directory($name, $this->config);
        }, $dirnames));
    }

    /**
     * @return Media[]
     */
    public function getMedias() {

        if(is_array($this->medias))
            return $this->medias;

        $filenames  = [];
        foreach($this->config->getImageExtensions() as $ext)
            $filenames = array_merge($filenames, array_filter(glob($this->path . '/*' . $ext), 'is_file'));

        $this->medias = array_values(array_map(function($name) {
            return new Media($name, $this->config);
        }, $filenames));

        return $this->medias;
    }

    /**
     * @return null|Media
     */
    public function getThumbnail() {

        $medias = $this->getMedias();

        if(count($medias) > 0)
            return reset($medias);

        foreach($this->getChildren() as $child)
            if($thumb = $child->getThumbnail())
                return $thumb;

        return null;
    }

    public function getName() {

        $segments   = explode('/', $this->path);

        return end($segments);
    }

    public function getDescription() {

        $descriptionFilePath    = $this->path . "/" . $this->config->getDescriptionFilename();
        return !is_file($descriptionFilePath) ? null : file_get_contents($descriptionFilePath);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function getRelativePath() {

        return str_replace($this->config->getFullMappedDirectory(), "", $this->path);
    }
}