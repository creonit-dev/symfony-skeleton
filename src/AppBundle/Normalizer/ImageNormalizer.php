<?php

namespace AppBundle\Normalizer;

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
        return $object->getUrl();
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Image;
    }
}