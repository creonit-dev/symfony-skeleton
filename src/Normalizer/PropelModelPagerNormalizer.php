<?php

namespace App\Normalizer;

use Propel\Runtime\Util\PropelModelPager;

class PropelModelPagerNormalizer extends AbstractNormalizer
{

    /**
     * @param PropelModelPager $object
     * @param null $format
     * @param array $context
     * @return array
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'items' => $this->serializer->normalize($object->getResults(), $format, $context),
            'pagination' => [
                'page' => $object->getPage(),
                'pages' => $object->getLastPage(),
                'total' => $object->getNbResults()
            ]
        ];
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
        return $data instanceof PropelModelPager;
    }
}