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

        $dirnames   = array_filter(glob($this->path . DIRECTORY_SEPARATOR . '*'), 'is_dir');
        $names = [];
        $numbers = [];
        foreach($dirnames as $dirname) {
            $data = explode(DIRECTORY_SEPARATOR, $dirname);
            $last = array_pop($data);
            if (is_numeric($last)) $numbers[$dirname] = $last;
            else $names[$dirname] = $last;
        }

        asort($names);
        arsort($numbers);

        $dirnames = array_merge(array_keys($names), array_keys($numbers));

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
        $extensions = $this->config->getImageExtensions();
        foreach($this->config->getImageExtensions() as $ext)
            $extensions[] = strtoupper($ext);

        foreach($extensions as $ext)
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


    public function getHashPath() {

        $data = explode('/', $this->getRelativePath());
        $data = array_map(function($item) {return base64_encode(str_replace('?', '__intermark', $item));}, $data);

        return implode("/", $data);
    }

    public static function unhashPath($path) {
        $data = explode('/', $path);
        $data = array_map(function($item) {return str_replace('__intermark', '?', base64_decode($item));}, $data);

        return implode("/", $data);
    }
}
