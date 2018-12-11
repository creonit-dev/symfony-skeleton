<?php


namespace AppBundle\Admin\Forms;

use Creonit\AdminBundle\Component\TableComponent;

class ChooseMailingTemplateTable extends TableComponent
{

    /**
     * @title Выберите шаблон для автоответа
     * @header
     * {{ button('Добавить шаблон', {size: 'sm', type: 'success', icon: 'clipboard'}) | open('Mailing.TemplateEditor') }}
     *
     * @cols Идентификатор, Заголовок
     *
     * \MailingTemplate
     * @entity Creonit\MailingBundle\Model\MailingTemplate
     * @sortable true
     *
     * @col {{ name | action('external', _key, title) | controls }}
     * @col {{ title }}
     *
     *
     */
    public function schema()
    {
    }
}