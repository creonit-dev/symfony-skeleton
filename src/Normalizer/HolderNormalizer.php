<?php

namespace App\Normalizer;

use App\Model\Holder;
use App\Service\InformService;
use Propel\Runtime\Exception\PropelException;

class HolderNormalizer extends AbstractNormalizer
{


    /**
     * @param Holder $inform
     * @param string|null $format
     * @param array $context
     * @return array
     * @throws PropelException
     */
    public function normalize($holder, $format = null, array $context = []): array
    {

        $data = [
            'id' => $holder->getId(),
        ];

        return $this->serializer->normalize($data, $format, $context);
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Holder;
    }
}