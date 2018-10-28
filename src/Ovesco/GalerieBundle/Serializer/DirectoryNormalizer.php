<?php

namespace Ovesco\GalerieBundle\Serializer;

use Ovesco\GalerieBundle\Model\Directory;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DirectoryNormalizer implements NormalizerInterface
{
    private $normalizer;

    public function __construct(MediaNormalizer $normalizer)
    {
        $this->normalizer   = $normalizer;
    }

    /**
     * @param Directory $directory
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($directory, $format = null, array $context = array())
    {
        $thumb  = $directory->getThumbnail();

        return [

            'name'      => $directory->getName(),
            'thumbnail' => $thumb ? $this->normalizer->normalize($thumb) : null,
            'path'      => $directory->getRelativePath(),
            'hashPath'  => $directory->getHashPath()
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Directory;
    }
}