<?php
/**
 * Created by PhpStorm.
 * User: makarov
 * Date: 06.07.2016
 * Time: 19:49
 */

namespace AppBundle\Admin\Forms;


use AppBundle\Model\FormResult;
use Creonit\AdminBundle\Component\EditorComponent;
use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;

class FormResultEditor extends EditorComponent
{
	/**
	 * @entity FormResult
	 * @title Результат заполнения формы
	 *
	 * @field created {load: 'entity.getCreatedAt("d.m.Y H:i")'}
	 * @field status:select
	 * @field user_id
	 * @field user_caption {load: 'entity.getUserCaption()'}
	 * @field answered {load: 'entity.isAnswered()'}
	 * @field link_from {load: 'entity.getLinkFrom()'}

	 *
	 * @template
	 * {{ form_title | panel | group('Форма') }}
	 * {{ created | panel | group('Дата и время') }}
	 * {{ status | select | group('Статус') }}
	 *
	 * {% if target_title %}
	 * 	 {{ target_title | panel | group('Объект') }}
	 * {% endif %}
	 *
	 * {{ component('Forms.FormResultFieldTable', {form_result_id: _key}) | group('Ответы') }}
	 *
	 * {% if user_id %}
	 *   {{ user_caption | panel | group('Пользователь') }}
	 * {% endif %}
	 *
	 * {{ link_from | raw | panel | group('URL с которой отправили форму') }}
	 * {{ ip_address | panel | group('IP-адрес') }}
	 *
	 */
	public function schema()
	{
		$this->getField('status')->parameters->set('options', FormResult::getStatuses());
	}

	/**
	 * @param ComponentRequest $request
	 * @param ComponentResponse $response
	 * @param FormResult $entity
     */
	public function validate(ComponentRequest $request, ComponentResponse $response, $entity)
	{
		if($request->data->get('answer') and !$entity->getEmail()){
			$response->error('Невозможно ответить на заявку — не указана электронная почта', 'answer');
		}
	}


	/**
	 * @param ComponentRequest $request
	 * @param ComponentResponse $response
	 * @param FormResult $entity
	 */
	public function preSave(ComponentRequest $request, ComponentResponse $response, $entity)
	{

		if($request->data->get('answer')){
			$entity->setAnsweredAt(date('Y-m-d H:i:s'));

			$mailing = $this->container->get('creonit_mailing');
			$message = $mailing->createMessage('Re: ' . $entity->getCaption(), ['form.answer' => [
				'content' => $entity->getShortContent(),
				'answer' => $request->data->get('answer'),
			] ]);

			$mailing->send($message, $entity->getEmail());
		}

	}



}