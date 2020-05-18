<?php

namespace App\Admin\Storage;

class StorageModule extends \Creonit\StorageBundle\Admin\StorageModule\StorageModule
{
    protected function configure()
    {
        $this
            ->setTitle('Управление контентом')
            ->setIcon('list')
            ->setTemplate('StorageTable');
    }
}