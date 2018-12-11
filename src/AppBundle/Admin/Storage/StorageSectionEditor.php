<?php

namespace AppBundle\Admin\Storage;

use AppBundle\Model\StorageSectionQuery;
use Creonit\AdminBundle\Component\EditorComponent;
use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;

class StorageSectionEditor extends EditorComponent
{

	/**
	 * @entity StorageSection
	 * @title Секция
	 *
	 * @field title {constraints: [NotBlank()]}
	 * @field section_id:select
	 *
	 * @template
	 *
	 * {{ title | text | group('Название') }}
	 * {{ section_id | select | group('Секция') }}
	 *
	 */
	public function schema()
	{
		$section_id = [];
		$section_id[] = '';
		foreach(StorageSectionQuery::create()->find() as $section){
		 	$section_id[$section->getId()] = $section->getTitle();
		}

		$this->getField('section_id')->parameters->set('options', $section_id);
	}

	public function preSave(ComponentRequest $request, ComponentResponse $response, $entity)
	{
		if(!$request->data->get('product_category_id')){
			$entity->setSectionId(null);
		}
	}
}