<?php

namespace AppBundle\Admin\Storage;

use AppBundle\Model\StorageSectionQuery;
use Creonit\AdminBundle\Component\EditorComponent;

class StorageEditor extends EditorComponent
{

	/**
	 * @entity Storage
	 * @title Блок
	 *
	 * @field title {constraints: [NotBlank()]}
	 * @field code {constraints: [NotBlank()]}
	 * @field section_id:select {constraints: [NotBlank()]}
	 *
	 * @template
	 *
	 * {{ section_id | select | group('Секция') }}
	 * {{ title | text | group('Название') }}
	 * {{ code | text | group('Идентификатор') }}
     * {{ multiresult | checkbox('Коллекция элементов') }}
	 * {{ (_key ? component('Storage.StorageFieldTable', {storage_id: _key}) : '<p>Сохраните блок, чтобы добавить поля</p>' | raw ) | group('Поля') }}
	 *
	 *
	 */
	public function schema()
	{
		$sections = [];
		$sections[] = [];
		foreach(StorageSectionQuery::create()->orderBySortableRank()->find() as $section){
			$sections[$section->getId()] = $section->getTitle();
		}
		$this->getField('section_id')->parameters->set('options', $sections);
	}
}