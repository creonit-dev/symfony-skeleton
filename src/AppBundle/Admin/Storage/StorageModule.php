<?php

namespace AppBundle\Admin\Storage;

use Creonit\AdminBundle\Module;

class StorageModule extends Module
{

	protected function configure()
	{
		$this
			->setTitle('Управление контентом')
			->setIcon('list')
			->setTemplate('StorageTable');
	}

	public function initialize()
	{
		$this->addComponent(new StorageTable());
		$this->addComponent(new StorageEditor());
		$this->addComponent(new StorageFieldTable());
		$this->addComponent(new StorageFieldEditor());
		$this->addComponent(new StorageFillEditor());
        $this->addComponent(new StorageMultiresultTable());
		$this->addComponent(new StorageSectionEditor());
	}
}