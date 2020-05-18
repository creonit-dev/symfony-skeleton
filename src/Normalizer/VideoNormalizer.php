<?php

namespace App\Normalizer;

use Creonit\MediaBundle\Exception\VideoSourceIsNotSupportedException;
use Creonit\MediaBundle\Model\Video;
use Creonit\MediaBundle\VideoHandler\YoutubeVideoData;

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
            'id' => $object->getId(),
            'source' => $object->getSource()
        ];

        try {
            $videoHandler = $this->container->get('creonit_media')->getVideoHandler($object);
            /** @var YoutubeVideoData $videoData */
            $videoData = $videoHandler->getVideoData($object);

            $data += [
                'code' => $videoData->getCode(),
                'image_url' => $videoData->getImageUrl(),
                'embed_url' => $videoData->getEmbedUrl()
            ];

        } catch (VideoSourceIsNotSupportedException $exception) {
        }

        return $this->serializer->normalize($data, $format, $context);
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Video;
    }
}