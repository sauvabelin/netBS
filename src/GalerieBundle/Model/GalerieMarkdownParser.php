<?php
namespace GalerieBundle\Model;


use GalerieBundle\Entity\Directory;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class GalerieMarkdownParser extends \Parsedown
{
    private $directory;

    private $cacheManager;

    public function __construct(Directory $directory, CacheManager $manager)
    {
        $this->directory    = $directory;
        $this->cacheManager = $manager;
    }

    protected function inlineImage($excerpt)
    {
        $image  = parent::inlineImage($excerpt);

        if ( ! isset($image))
            return null;

        $path   = $this->directory->getsearchPath() . $image['element']['attributes']['src'];
        $image['element']['attributes']['src'] = $this->cacheManager->getBrowserPath($path, 'bignail');

        return $image;
    }
}