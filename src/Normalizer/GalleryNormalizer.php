<?php

namespace App\Normalizer;

use Creonit\MediaBundle\Model\Gallery;
use Creonit\MediaBundle\Model\GalleryItemQuery;

class GalleryNormalizer extends AbstractNormalizer
{

    /**
     * @inheritDoc
     * @param Gallery $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $items = GalleryItemQuery::create()
            ->filterByGallery($object)
            ->filterByVisible(true)
            ->orderBySortableRank()
            ->find();

        return $this->serializer->normalize($items, $format, $context);
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Gallery;
    }
}