<?php

namespace AppBundle\Model;

use AppBundle\Model\Base\Form as BaseForm;

/**
 * Skeleton subclass for representing a row from the 'form' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Form extends BaseForm
{

    //TO-DO генерацию js
    //TO-DO radio group
    //TO-DO checkbox
    //TO-DO checkbox group
    //TO-DO select

    const FIELD_TEXT = 1;
    const FIELD_TEXTAREA = 2;
    const FIELD_SELECT = 3;
    const FIELD_FILE = 4;
    const FIELD_CHECKBOX = 5;

    const VALIDATION_ALPHABETICAL = 1;
    const VALIDATION_DIGITS = 2;
    const VALIDATION_EMAIL = 3;
    const VALIDATION_PHONE = 4;

    public static $validation_types = [
        1 => 'Буквы и пробелы',
        2 => 'Цифры',
        3 => 'Email',
        4 => 'Телефон'
    ];

    public static function getValidationTypes() {
        return self::$validation_types;
    }

    public static $field_types = [
        self::FIELD_TEXT => 'Однострочный текст',
        self::FIELD_TEXTAREA => 'Многострочный текст',
        self::FIELD_SELECT => 'Выбор из списка',
        self::FIELD_FILE => 'Файл',
        self::FIELD_CHECKBOX => 'Чекбокс',
    ];

    public static function getTypes() {
        return self::$field_types;
    }

    public static $field_type_names = [
        self::FIELD_TEXT => 'text',
        self::FIELD_TEXTAREA => 'textarea',
        self::FIELD_SELECT => 'select',
        self::FIELD_FILE => 'file',
        self::FIELD_CHECKBOX => 'checkbox'
    ];

    public static function getTypeNames() {
        return self::$field_type_names;
    }

    public static function getTypeName($type){
        return isset(self::getTypeNames()[$type]) ? self::getTypeNames()[$type] : '';
    }

    public static $field_validate_types = [
        1 => 'alphabetical',
        2 => 'digits',
        3 => 'email',
        4 => 'ru_phone',
        5 => 'postal_code'
    ];

    public static function getValidateType($type){
        return isset(self::getValidationTypes()[$type]) ? self::getValidationTypes()[$type] : '';
    }

    /**
     * @return FormField[]|\Propel\Runtime\Collection\ObjectCollection
     */
    public function getFields(){
        return FormFieldQuery::create()->filterByVisible(true)->filterByFormId($this->id)->filterByVisible(true)->orderBySortableRank()->find();
    }

}
