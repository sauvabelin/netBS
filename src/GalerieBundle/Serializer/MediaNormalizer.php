<?php

namespace GalerieBundle\Serializer;

use GalerieBundle\Entity\Media;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MediaNormalizer implements NormalizerInterface
{
    private $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
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
            'filename'  => $media->getFilename(),
            'thumbnail' => $this->cacheManager->getBrowserPath($media->getsearchPath(), 'thumbnail'),
            'bignail'   => $this->cacheManager->getBrowserPath($media->getsearchPath(), 'bignail')
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Media;
    }
}