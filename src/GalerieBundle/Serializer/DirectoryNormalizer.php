<?php

namespace GalerieBundle\Serializer;

use GalerieBundle\Entity\Directory;
use GalerieBundle\Model\GalerieMarkdownParser;
use GalerieBundle\Service\GalerieTree;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DirectoryNormalizer implements NormalizerInterface
{
    private $tree;

    private $cacheManager;

    public function __construct(GalerieTree $tree, CacheManager $manager)
    {
        $this->tree         = $tree;
        $this->cacheManager = $manager;
    }

    /**
     * @param Directory $directory
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($directory, $format = null, array $context = array())
    {
        $thumbnail      = $this->tree->getThumbnail($directory);
        $parser         = new GalerieMarkdownParser($directory, $this->cacheManager);

        return [
            'name'          => $directory->getName(),
            'description'   => $parser->text($directory->getDescription()),
            'path'          => $directory->getWebdavUrl(),
            'thumbnail'     => $thumbnail !== null
                ? $this->cacheManager->getBrowserPath($thumbnail->getsearchPath(), 'thumbnail')
                : null
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Directory;
    }
}