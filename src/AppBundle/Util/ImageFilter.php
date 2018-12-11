<?php

namespace AppBundle\Util;

use Creonit\MediaBundle\Model\Image;
use Gregwar\ImageBundle\ImageHandler;
use Gregwar\ImageBundle\Services\ImageHandling;

class ImageFilter
{
    /**
     * @var Image|null
     */
    protected $image;
    /**
     * @var array
     */
    protected $settings;

    public function __construct(Image $image = null, array $settings)
    {
        $this->image = $image;
        $this->settings = $settings;
    }

    public function build(ImageHandling $handling)
    {
        if (null === $this->image) {
            return null;
        }

        $image = $handling->open($this->image->getRelativeUrl());
        $images = [];

        /** @var \Callable $setting */
        foreach ($this->settings as $key => $setting) {
            $imageResult = $setting($image);
            if($imageResult instanceof ImageHandler) {
                $imageResult = $imageResult->png();
            }
            $images[$key] = $imageResult;
        }

        return $images;
    }
}