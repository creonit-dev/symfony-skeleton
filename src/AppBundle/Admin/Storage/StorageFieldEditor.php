<?php

namespace AppBundle\Admin\Storage;

use AppBundle\Model\StorageField;
use Creonit\AdminBundle\Component\EditorComponent;
use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;

class StorageFieldEditor extends EditorComponent
{

	/**
	 * @entity StorageField
	 * @title Поле
	 *
	 * @field title {constraints: [NotBlank()]}
	 * @field code {constraints: [NotBlank()]}
	 * @field type:select {constraints: [NotBlank()]}
	 *
	 * @template
	 *
	 * {{ type | select | group('Тип поля') }}
	 * {{ title | text | group('Название') }}
	 * {{ code | text | group('Идентификатор') }}
	 *
	 */
	public function schema()
	{
		$this->getField('type')->parameters->set('options', StorageField::getTypes());
	}

	public function preSave(ComponentRequest $request, ComponentResponse $response, $entity)
	{
		if($entity->isNew()){
			$entity->setStorageId($request->query->get('storage_id'));
		}
	}

}