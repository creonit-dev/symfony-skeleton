<?php

namespace AppBundle\Admin\Storage;

use AppBundle\Model\StorageFieldQuery;
use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;
use Creonit\AdminBundle\Component\Scope\Scope;
use Creonit\AdminBundle\Component\TableComponent;
use Propel\Runtime\ActiveQuery\Criteria;

class StorageFieldTable extends TableComponent
{

	/**
	 * @title Блоки
	 * @header
	 *
	 * {{ button('Добавить поле', {size: 'sm', type: 'success'}) | open('Storage.StorageFieldEditor', {storage_id: _query.storage_id }) }}
	 *
	 * @action copy(options){
	 *      var $row = this.findRowById(options.rowId);
	 *
	 *      this.request('copy', $.extend({storage_field_id: options.key}, this.getQuery()), {state: $row.hasClass('success')});
	 *      this.loadData();
	 * }
	 *
	 * @cols Товары, Индентификатор, .
	 *
	 * \StorageField
	 * @sortable true
	 * @field title
	 *
	 * @col {{ title | open('Storage.StorageFieldEditor', {key: _key}) | controls(button('', {icon: 'clone', size: 'xs'}) | action('copy', {key: _key, rowId: _row_id})) }}
	 * @col {{ code }}
	 * @col {{ buttons(_visible() ~ _delete())  }}
	 *
	 */
	public function schema()
	{
		$this->setHandler('copy', function (ComponentRequest $request, ComponentResponse $response) {
			$block = StorageFieldQuery::create()->findPk($request->query->get('storage_field_id')) or $response->flushError('Блок не найден');

			$last = StorageFieldQuery::create()->filterByStorage($block)->orderBySortableRank(Criteria::DESC)->findOne();

			$copy = $block->copy(true);
			$copy
				->setTitle($block->getTitle() .' (Копия)')
				->setCode($block->getCode() .'_copy')
				->setSortableRank($last->getSortableRank() + 1)
				->save();
		});
	}

	/**
	 * @param ComponentRequest $request
	 * @param ComponentResponse $response
	 * @param StorageFieldQuery $query
	 * @param Scope $scope
	 * @param $relation
	 * @param $relationValue
	 * @param $level
	 */
	protected function filter(ComponentRequest $request, ComponentResponse $response, $query, Scope $scope, $relation, $relationValue, $level)
	{
		$query->filterByStorageId($request->query->get('storage_id'));
	}
}