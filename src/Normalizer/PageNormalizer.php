<?php

namespace App\Normalizer;

use Creonit\PageBundle\Model\Page;
use Creonit\StorageBundle\Storage\Storage;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;

class PageNormalizer extends AbstractNormalizer
{
    const GROUP_DETAIL = 'page_detail';

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param Page $object Object to normalize
     * @param string $format Format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array|string|int|float|bool
     *
     * @throws InvalidArgumentException   Occurs when the object given is not an attempted type for the normalizer
     * @throws CircularReferenceException Occurs when the normalizer detects a circular reference when no circular
     *                                    reference handler can fix it
     * @throws LogicException             Occurs when the normalizer is not called in an expected context
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $url = '';
        if ($object->isTypeRoute()) {
            try {
                $url = $this->container->get('router')->generate($object->getName(), [], RouterInterface::ABSOLUTE_URL);
            } catch (\Exception $exception) {
            }

        } else {
            $url = $object->getUrl();
        }

        $data = [
            'id' => $object->getId(),
            'slug' => $object->getSlug(),
            'title' => $object->getTitle(),
            'url' => $url,
            'createdAt' => $object->getCreatedAt(),
            'updatedAt' => $object->getUpdatedAt(),
            'configuration' => $this->container->get(Storage::class)->get('pageConfiguration', null, $object, true)
        ];

        if ($this->hasGroup($context, self::GROUP_DETAIL)) {
            $data['content'] = $object->getContent();
            $data['extraContent'] = $object->getExtraContent();
            $data['metaTitle'] = $object->getMetaTitle();
            $data['metaDescription'] = $object->getMetaDescription();
            $data['metaKeywords'] = $object->getMetaKeywords();
        }

        return $this->serializer->normalize($data, $format, $context);
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data Data to normalize
     * @param string $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Page;
    }
}