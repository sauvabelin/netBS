<?php

namespace TenteBundle\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use TenteBundle\Entity\DrawingPart;
use TenteBundle\Entity\Tente;
use TenteBundle\Entity\TenteModel;

class TenteModelNormalizer implements NormalizerInterface
{
    /**
     * @param TenteModel $model
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($model, $format = null, array $context = array())
    {
        $tentes = array_map(function(Tente $tente) { return $tente->getNumero();}, $model->getTentes()->toArray());
        $drawings = array_map(function(DrawingPart $part) {
            return [
                'name' => $part->getNom(),
                'image' => $part->getImage(),
            ];
        }, $model->getDrawingParts()->toArray());

        return [
            'name' => $model->getName(),
            'tentes' => $tentes,
            'formData' => $model->getParsedForm(),
            'drawingParts' => $drawings,
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof TenteModel;
    }
}
