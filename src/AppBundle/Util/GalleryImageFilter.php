<?php

namespace AppBundle\Util;

use Creonit\MediaBundle\Model\Gallery;
use Creonit\MediaBundle\Model\GalleryItem;
use Gregwar\ImageBundle\Services\ImageHandling;

class GalleryImageFilter
{

    /**
     * @var Gallery|null
     */
    protected $gallery;
    /**
     * @var array
     */
    protected $settings;

    public function __construct(Gallery $gallery = null, array $settings)
    {
        $this->gallery = $gallery;
        $this->settings = $settings;
    }

    public function build(ImageHandling $handling)
    {
        if (null === $this->gallery) {
            return null;
        }

        $result = [];
        /** @var GalleryItem $item */
        foreach ($this->gallery->getList() as $item) {
            if ($image = $item->getImage()) {
                $result[] = new ImageFilter($image, $this->settings);
            }
        }

        return $result;
    }

}