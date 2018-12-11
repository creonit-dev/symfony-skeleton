<?php

namespace AppBundle\Model;

use AppBundle\Model\Base\FormResult as BaseFormResult;

/**
 * Skeleton subclass for representing a row from the 'form_result' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class FormResult extends BaseFormResult
{

    const STATUS_NEW = 1;
    const STATUS_PROGRESS = 2;
    const STATUS_PROCESSED = 3;

    protected $content;

    public static $statuses = [
        self::STATUS_NEW => 'Новая',
        self::STATUS_PROGRESS => 'В обработке',
        self::STATUS_PROCESSED => 'Обработана'
    ];

    public static function getStatuses(){
        return self::$statuses;
    }

    public function getCaption(){
        return $this->form_title;
    }

    public function getStatusCaption(){
        return isset(self::getStatuses()[$this->status]) ? self::getStatuses()[$this->status] : '';
    }

    public function isAnswered(){
        return $this->getAnsweredAt() ? true : false;
    }

    public function getUserCaption(){
        return $this->user_id ? ($this->getUser()->getTitleFull().' ('.$this->getUser()->getEmail().')') : '';
    }

    public function getLinkFrom(){
        return '<a target="_blank" href="'.$this->url_from.'">'.$this->url_from.'</a>';
    }

    public function getShortContent(){
        $content = '<table cellpadding="5" border="1">';

        foreach ($this->getFormResultFieldsJoinFormField() as $field){
            if($field->getFormField()->isFile()) continue;
            $content .= "<tr><td>{$field->getFieldTitle()}</td><td>{$field->getAnswerValue()}</td>";
        }

        $content .= '</table>';
        return $content;
    }

    public function getStringContent() {
        $content = $this->getForm()->getTitle() . ': ';
        $first = true;
        foreach ($this->getFormResultFieldsJoinFormField() as $field) {
            if($field->getFormField()->isFile()) continue;

            $content .= ($first? '' : ', ') . $field->getFieldTitle() . ': ' . $field->getAnswerValue();
            $first = false;
        }

        if($this->getTargetId()){
            $object = $this->getHolder()->getObject();
            $objectType = 'Объект';
            if($object instanceof Card){
                $objectType = 'Карта';
            }

            $content .= ', '.$objectType.': ' . $object->getTitle();
        }

        return $content;
    }

    public function getContent(){
        if(!$this->content){

            $content = '<table cellpadding="5" border="1">';

            foreach ($this->getFormResultFieldsJoinFormField() as $field){
                $content .= "<tr><td>{$field->getFieldTitle()}</td><td>{$field->getAnswerValue()}</td>";
            }

            $content .= '</table>';
            $this->content = $content;
        }

        return $this->content;
    }

    public function setContent($content){
        $this->content = $content;
        return $this;
    }

    public function getFieldValue($field){
        return $this->cache('result_field_'.$field, function() use($field){
            $field = FormResultFieldQuery::create()
                ->useFormFieldQuery()
                ->filterByCode($field)
                ->endUse()
                ->filterByFormResult($this)
                ->findOne();

            if($field){
                return $field->getValue();
            }else{
                return '';
            }

        });
    }

    public function getEmail(){
        return $this->getFieldValue('email');
    }

    public function getClub(){
        return $this->cache(__METHOD__, function(){
            if($this->target_id && $this->getHolder()->getCardId()){
                return $this->getHolder()->getCard()->getClub()->getTitle();
            }else{
                return $this->getFieldValue('club');
            }
        });
    }

    public function getNameFull(){
        if($this->getFieldValue('name')){
            $name = trim($this->getFieldValue('name'));
        }else{
            $name = trim($this->getFieldValue('lastname') .' '. $this->getFieldValue('firstname') .' '. $this->getFieldValue('middlename'));
        }

        if(!$name && $this->user_id){
            $name = $this->getUser()->getTitleFull();
        }

        return $name;
    }

    public function getName(){
        return $this->getFieldValue('name');
    }

    public function getContract(){
        return $this->cache(__METHOD__, function(){
            if($field = FormResultFieldQuery::create()
                ->useFormFieldQuery()
                ->filterByCode('contract')
                ->endUse()
                ->filterByFormResult($this)
                ->findOne()
            ){
                return $field->getValue();
            }
        });
    }

    public function getTargetCaption(){
        $holder = $this->getHolder();
        if(!$holder) return '';
        switch(true){
            case $holder->getClubId():
                return 'Клуб';
            case $holder->getCardId():
                return 'Карта';
            case $holder->getVacancyId():
                return 'Вакансия';
            case $holder->getPageId():
                return 'Страница';
            default:
                return 'Object not found';
        }
    }

    public function getFieldAnswer($field){
        if(is_string($field)){
            $field = FormFieldQuery::create()->filterByCode($field)->filterByForm($this->getForm())->select('id')->findOne();
        }

        return FormResultFieldQuery::create()->filterByFormResult($this)->filterByFieldId($field)->findOne() ? FormResultFieldQuery::create()->filterByFormResult($this)->filterByFieldId($field)->findOne()->getAnswerValue() : '';
    }

}
