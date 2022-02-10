<?php

namespace Ovesco\GalerieBundle\Serializer;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Ovesco\GalerieBundle\Model\GalerieConfig;
use Ovesco\GalerieBundle\Model\Media;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MediaNormalizer implements NormalizerInterface
{
    private $webPath;

    private $cacheManager;

    private $assets;

    private $config;

    public function __construct($webPath, CacheManager $cacheManager, AssetExtension $extension, GalerieConfig $config)
    {
        $this->webPath      = $webPath;
        $this->cacheManager = $cacheManager;
        $this->assets       = $extension;
        $this->config       = $config;
    }

    /**
     * @param Media $media
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($media, $format = null, array $context = array())
    {
        return [

            'filename'  => $media->getName(),
            'size'      => $media->getSize(),
            'timestamp' => $media->getTimestamp(),
            'thumbnail' => $this->assets->getAssetUrl($this->cacheManager->getBrowserPath(base64_encode($media->getRelativePath()), 'thumbnail')),
            'bignail'   => $this->webPath . $this->assets->getAssetUrl($this->config->getMappedDirectory() . $media->getRelativePath())
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Media;
    }
}