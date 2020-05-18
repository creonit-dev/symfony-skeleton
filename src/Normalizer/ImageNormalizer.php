<?php

namespace App\Normalizer;

use Creonit\MediaBundle\Model\Image;

class ImageNormalizer extends AbstractNormalizer
{
    /**
     * @param Image $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $data = [
            'file' => $object->getFile(),
        ];

        return $this->serializer->normalize($data, $format, $context);
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Image;
    }
}