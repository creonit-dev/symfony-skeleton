<?php
/**
 * Created by PhpStorm.
 * User: makarov
 * Date: 06.07.2016
 * Time: 14:24
 */

namespace AppBundle\Admin\Forms;


use AppBundle\Model\FormFieldOptionQuery;
use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;
use Creonit\AdminBundle\Component\Scope\Scope;
use Creonit\AdminBundle\Component\TableComponent;

class FormFieldOptionTable extends TableComponent
{

	/**
	 * @title
	 * @header
	 * {{ button('Добавить вариант', {type: 'success', icon: 'list', size: 'sm'}) | open('Forms.FormFieldOptionEditor', {form_field_id: _query.form_field_id}) }}
	 *
	 * @cols Название, .
	 * 
	 * \FormFieldOption
	 * @sortable true
	 *
	 * @col {{ title | open('Forms.FormFieldOptionEditor', {key: _key}) | controls }}
	 * @col {{ buttons(_visible() ~ _delete()) }}
	 *
	 *
	 */
	public function schema()
	{
	}

	/**
	 * @param ComponentRequest $request
	 * @param ComponentResponse $response
	 * @param FormFieldOptionQuery $query
	 * @param Scope $scope
	 * @param $relation
	 * @param $relationValue
	 * @param $level
	 */
	protected function filter(ComponentRequest $request, ComponentResponse $response, $query, Scope $scope, $relation, $relationValue, $level)
	{
		$query->filterByFieldId($request->query->get('form_field_id'));
	}
}