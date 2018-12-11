<?php


namespace AppBundle\Admin\Forms;


use Creonit\AdminBundle\Component\TableComponent;

class ChooseFormTable extends TableComponent
{


	/**
	 * @title Выберите форму
	 * @cols Название, Идентификатор
	 *
	 * \Form
	 * @pagination 100    
	 * @col {{ title | action('external', _key, title) }}
	 * @col {{ code }}
	 *
	 */
	public function schema()
	{
	}
}