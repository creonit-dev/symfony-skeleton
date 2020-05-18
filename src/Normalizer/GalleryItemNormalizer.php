<?php

namespace App\Normalizer;

use Creonit\MediaBundle\Model\GalleryItem;
use Propel\Runtime\Exception\PropelException;

class GalleryItemNormalizer extends AbstractNormalizer
{
    const GROUP_WITH_IDS = 'gallery_item_with_ids';
    const GROUP_COVER = 'gallery_item_cover';

    /**
     * @inheritDoc
     * @param GalleryItem $object
     * @throws PropelException
     */
    public function normalize($object, $format = null, array $context = [])
    {
        // Добавление id требует возвращение массива вместо строки. Поэтому, чтобы избежать поломок на фронтенде,
        // была добавлена соответствующая группа. Id нужен для реализации удаления отдельных фото/видео из галереи.
        if ($this->hasGroup($context, self::GROUP_WITH_IDS)) {
            return $this->normalizeWithIds($object, null, $context);
        }

        if ($image = $object->getImage()) {
            return $this->serializer->normalize($image, $format, $context);
        }
        if ($image = $object->getVideo()) {
            return $this->serializer->normalize($image, $format, $context);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof GalleryItem;
    }

    /**
     * @param GalleryItem $object
     * @return array
     * @throws PropelException
     */
    private function normalizeWithIds(GalleryItem $object, $format = null, array $context = []): array
    {
        $data = [
            'id' => $object->getId(),
            'image' => null,
        ];

        if ($this->hasGroup($context, self::GROUP_COVER)) {
            $data['is_cover'] = $this->container->get('app.performer')->checkIsCover($object);
        }

        if ($image = $object->getImage()) {
            $data['image'] = $image;
        }
        if ($image = $object->getVideo()) {
            $data['image'] = $image;
        }

        return $this->serializer->normalize($data, $format, $context);
    }
}