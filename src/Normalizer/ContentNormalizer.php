<?php


namespace App\Normalizer;


use App\Model\ContentBlock;
use App\Model\ContentBlockQuery;
use Creonit\ContentBundle\Model\Content;

class ContentNormalizer extends AbstractNormalizer
{
    const GROUP_DETAIL = 'content_detail';

    public function normalize($object, string $format = null, array $context = [])
    {
        $data = [
            'id' => $object->getId(),
            'text' => $object->getText(),
        ];

        if ($this->hasGroup($context, static::GROUP_DETAIL)) {
            $blocks = ContentBlockQuery::create()->filterByContent($object)->filterByVisible(true)->orderBySortableRank()->find();

            $blockMap = [];
            foreach ($blocks as $block) {
                $blockMap[$block->getParentId() ?: '_'][] = $block;
            }

            $data += [
                'blocks' => $blockMap['_'] ?? [],
            ];

            $context = $context + ['blockMap' => $blockMap];
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
    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Content;
    }
}