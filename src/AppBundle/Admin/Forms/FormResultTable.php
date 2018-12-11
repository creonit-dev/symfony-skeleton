<?php
/**
 * Created by PhpStorm.
 * User: makarov
 * Date: 06.07.2016
 * Time: 14:01
 */

namespace AppBundle\Admin\Forms;


use AppBundle\Model\FormQuery;
use AppBundle\Model\FormResult;
use AppBundle\Model\FormResultQuery;
use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;
use Creonit\AdminBundle\Component\Scope\Scope;
use Creonit\AdminBundle\Component\TableComponent;
use Propel\Runtime\ActiveQuery\Criteria;

class FormResultTable extends TableComponent
{


	/**
	 * @title Результаты форм
	 * @field type_id:select
	 * @field status:select
	 * @header
	 *  <form class="form-inline pull-right">
	 * {{ type_id | select | group('Тип') }}
	 * {{ status | select | group('Статус') }}
	 * {{ submit('Обновить', {size: 'sm'}) }}
	 * </form>
	 *
	 * {{ button('Формы', {icon: 'fa-list-alt', size: 'sm'}) | open('Forms.FormTable') }}
	 *
	 * @cols Название, Статус, Время отправки, .
	 *
	 * \FormResult
	 * @field caption {load: 'entity.getCaption()'}
	 * @field status_caption {load: 'entity.getStatusCaption()'}
	 * @field created {load: 'entity.getCreatedAt("d.m.Y H:i")'}
	 *
	 * @pagination 100
	 * @col {{ caption | open('Forms.FormResultEditor', {key: _key}) | controls }}
	 * @col {{ status_caption }}
	 * @col {{ created }}
     * @col {{ _delete() }}
	 *
	 */
	public function schema()
	{
		$formTypes[0] = 'Все типы';
		foreach(FormQuery::create()->filterByVisible(true)->find() as $formType) {
			$formTypes[$formType->getId()] = $formType->getTitle();
		}
		$this->getField('type_id')->parameters->set('options', $formTypes);

		$formStatuses[0] = 'Все';
		foreach(FormResult::getStatuses() as $key => $status) {
			$formStatuses[$key] = $status;
		}

		$this->getField('status')->parameters->set('options', $formStatuses);
	}

	/**
	 * @param ComponentRequest $request
	 * @param ComponentResponse $response
	 * @param FormResultQuery $query
	 * @param Scope $scope
	 * @param $relation
	 * @param $relationValue
	 * @param $level
	 */
	protected function filter(ComponentRequest $request, ComponentResponse $response, $query, Scope $scope, $relation, $relationValue, $level)
	{
		$query->orderByCreatedAt(Criteria::DESC);

		if($typeId = $request->query->get('type_id')){
			$query
				->filterByFormId($typeId)
			;
		}

		if($status = $request->query->get('status')){
			$query
				->filterByStatus($status)
			;
		}
	}

}