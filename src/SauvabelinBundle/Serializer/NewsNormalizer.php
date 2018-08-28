<?php

namespace SauvabelinBundle\Serializer;

use SauvabelinBundle\Entity\News;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NewsNormalizer implements NormalizerInterface
{
    /**
     * @param News $news
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($news, $format = null, array $context = array())
    {
        return [
            'id'        => $news->getId(),
            'titre'     => $news->getTitre(),
            'contenu'   => $news->getContenu(),
            'user'      => $news->getUser()->__toString(),
            'importante'=> $news->isImportante(),
            'channel'   => [
                'nom'   => $news->getChannel()->getNom(),
                'color' => $news->getChannel()->getColor()
            ],
            'date'      => $news->getCreatedAt()->format('c')
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof News;
    }
}