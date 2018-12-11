<?php
/**
 * Created by PhpStorm.
 * User: makarov
 * Date: 06.07.2016
 * Time: 14:08
 */

namespace AppBundle\Admin\Forms;


use AppBundle\Model\Form;
use Creonit\AdminBundle\Component\EditorComponent;
use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;

class FormFieldEditor extends EditorComponent
{

	/**
	 * @entity FormField
	 * @title Поле формы
	 *
	 * @field title {constraints: [NotBlank()]}
	 * @field type:select {constraints: [NotBlank()]}
	 * @field validation_type:select
	 *
	 * @template
	 * {{ title | text | group('Название') }}
	 * {{ code | text | group('Идентификатор') }}
	 * {{ type | select | group('Тип') }}
	 * {{ validation_type | select | group('Тип проверки') }}
	 * {{ invalid_error | text | group('Ошибка если поле заполнено некорректно') }}
	 * {{ required_error | text | group('Ошибка если поле не заполнено') }}
	 * {{ required | checkbox('Обязательное') }} 
	 * 
	 * {% if _key and type.value == 3 %}
	 *      {{ component('Forms.FormFieldOptionTable', {form_field_id: _key}) | group('Варианты ответа') }}
	 * {% endif %}
	 *
	 */
	public function schema()
	{
		$this->getField('type')->parameters->set('options', Form::getTypes());
		$this->getField('validation_type')->parameters->set('options', array_merge([0 => '',], Form::getValidationTypes()));
	}

	public function preSave(ComponentRequest $request, ComponentResponse $response, $entity)
	{

		if($entity->isNew()){
			$entity->setFormId($request->query->get('form_id'));
		}

	}


}