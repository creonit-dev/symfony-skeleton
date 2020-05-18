<?php

namespace App\Admin\Content;

use Creonit\ContentBundle\Admin\ContentBlockModule\AbstractContentBlockModule;

class ContentModule extends AbstractContentBlockModule
{
    const BLOCK_MIN_SIZE = 1;
    const BLOCK_MAX_SIZE = 3;

    protected function configure()
    {
        $this
            ->setName('ContentBlockType')
            ->setVisible(false);

        $this->addBlockType(TextBlockEditor::class);

        $this->setBlockMinSize(static::BLOCK_MIN_SIZE);
        $this->setBlockMaxSize(static::BLOCK_MAX_SIZE);
    }
}
