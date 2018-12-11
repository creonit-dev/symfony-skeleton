<?php

namespace AppBundle\Model;

use AppBundle\Model\Base\FormResultField as BaseFormResultField;

/**
 * Skeleton subclass for representing a row from the 'form_result_field' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class FormResultField extends BaseFormResultField
{

    public function getFieldTitle(){
        return $this->getFormField()->getTitle();
    }

    public function getAnswerValue(){
        if($this->getFormField()->isFile()){
            return '<a target="_blank" href="'.$this->getFilePath().'">Скачать файл</a>';
        }else{
            return $this->value;
        }
    }

}
