<?php

namespace App\Admin\Content;

use Creonit\ContentBundle\Admin\ContentBlockModule\AbstractContentBlockEditor;
use Creonit\ContentBundle\Admin\ContentBlockModule\ContentBlockPreview;
use Creonit\MediaBundle\Service\MediaService;

class TextBlockEditor extends AbstractContentBlockEditor
{
    const TITLE = 'Текст';
    const ICON = 'align-left';
    const SECTION = 'text';

    /**
     * @template
     * {{ data | textedit }}
     */
    public function schema()
    {
    }

    public function getBlockPreview($block)
    {
        return new ContentBlockPreview(preg_replace('/[\n\r]+/u', '<br>', strip_tags($block->getData())));
    }
}
