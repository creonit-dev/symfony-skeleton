<?php

namespace App\Normalizer;

use Creonit\MediaBundle\Model\File;
use Creonit\MediaBundle\Service\MediaService;

class FileNormalizer extends AbstractNormalizer
{
    /**
     * @var MediaService
     */
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * @param File $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $data = [
            'url' => $object->getUrl(),
            'name' => $object->getName(),
            'originalName' => $object->getOriginalName(),
            'extension' => $object->getExtension(),
            'size' => $this->mediaService->formatFileSize($object->getSize()),
        ];

        return $data;
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof File;
    }
}