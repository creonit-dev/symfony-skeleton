<?php

namespace AppBundle\Normalizer;

use Creonit\MediaBundle\Model\File;

class FileNormalizer extends AbstractNormalizer
{
    /**
     * @param File $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'url' => $object->getUrl(),
            'name' => $object->getName(),
            'original_name' => $object->getOriginalName(),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof File;
    }
}