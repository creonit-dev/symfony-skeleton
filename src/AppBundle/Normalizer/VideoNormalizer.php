<?php

namespace AppBundle\Normalizer;

use Creonit\MediaBundle\Model\Video;

class VideoNormalizer extends AbstractNormalizer
{
    /**
     * @param Video $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = [])
    {

        $data = [
            'image_url' => $object->getPreviewUrl(),
            'url' => $object->getUrl(),
        ];
        preg_match('/(?:\?v=([\w\d_-]+)|.be\/([\w\d_-]+))/i', $object->getUrl(), $match);
        if ($match) {
            $data['code'] = $match[1] ? $match[1] : $match[2];
        }

        return $this->serializer->normalize($data, $format, $context);
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Video;
    }
}