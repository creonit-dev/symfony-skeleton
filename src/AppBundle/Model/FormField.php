<?php

namespace AppBundle\Model;

use AppBundle\Model\Base\FormField as BaseFormField;

/**
 * Skeleton subclass for representing a row from the 'form_field' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class FormField extends BaseFormField
{

    public function getFieldName(){
        return $this->code ?: 'field_'.$this->id;
    }

    public function getTypeCaption(){
        return isset(Form::getTypes()[$this->type]) ? Form::getTypes()[$this->type] : '';
    }

    public function isFile(){
        return Form::getTypeName($this->getType()) == 'file';
    }

}
