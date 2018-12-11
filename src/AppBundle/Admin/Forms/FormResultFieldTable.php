<?php
/**
 * Created by PhpStorm.
 * User: makarov
 * Date: 06.07.2016
 * Time: 20:37
 */

namespace AppBundle\Admin\Forms;


use AppBundle\Model\FormResultFieldQuery;
use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;
use Creonit\AdminBundle\Component\Scope\Scope;
use Creonit\AdminBundle\Component\TableComponent;

class FormResultFieldTable extends TableComponent
{

	/**
	 * @title Поля формы
	 * @header
	 * {{ button('Добавить поле', {type: 'success', icon: 'list', size: 'sm'}) | open('Forms.FormFieldEditor', {form_id: _query.form_id}) }}
	 *
	 * @cols Поле, Ответ
	 *
	 * \FormResultField
	 * @sortable true
	 * @field title {load: 'entity.getFieldTitle()'}
	 * @field answer {load: 'entity.getAnswerValue()'}
	 *
	 * @col {{ title }}
	 * @col {{ answer | raw }}
	 *
	 *
	 */
	public function schema()
	{
	}

	/**
	 * @param ComponentRequest $request
	 * @param ComponentResponse $response
	 * @param FormResultFieldQuery $query
	 * @param Scope $scope
	 * @param $relation
	 * @param $relationValue
	 * @param $level
	 */
	protected function filter(ComponentRequest $request, ComponentResponse $response, $query, Scope $scope, $relation, $relationValue, $level)
	{
		$query->filterByResultId($request->query->get('form_result_id'))->orderBySortableRank();
	}
}