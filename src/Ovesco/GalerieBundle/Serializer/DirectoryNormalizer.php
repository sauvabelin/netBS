<?php

namespace Ovesco\GalerieBundle\Serializer;

use Doctrine\ORM\EntityManager;
use Ovesco\GalerieBundle\Model\Directory;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DirectoryNormalizer implements NormalizerInterface
{
    private $normalizer;

    private $manager;

    public function __construct(MediaNormalizer $normalizer, EntityManager $manager)
    {
        $this->normalizer   = $normalizer;
        $this->manager      = $manager;
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
        $count = $this->manager->getRepository('OvescoGalerieBundle:DirectoryView')->count([
            'path' => $directory->getPath()
        ]);

        return [

            'count'     => $count,
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
