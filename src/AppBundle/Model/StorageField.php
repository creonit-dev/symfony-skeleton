<?php

namespace AppBundle\Model;

use AppBundle\Model\Base\StorageField as BaseStorageField;

/**
 * Skeleton subclass for representing a row from the 'storage_field' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class StorageField extends BaseStorageField
{

    public static $field_types = [
        1 => 'Текстовое поле',
        2 => 'Многострочный текст',
        3 => 'HTML текст',
        4 => 'Чекбокс',
        //5 => 'Выбор из списка значений',
        6 => 'Файл',
        7 => 'Изображение',
        8 => 'Видео',
        9 => 'Краска',
    ];

    public static $field_type_names = [
        1 => 'text',
        2 => 'textarea',
        3 => 'textedit',
        4 => 'checkbox',
        //5 => 'select',
        6 => 'file',
        7 => 'image',
        //8 => 'gallery',
        8 => 'video',
        9 => 'product',
    ];

    public static function getTypes() {
        return self::$field_types;
    }

    public static function getTypeNames() {
        return self::$field_type_names;
    }

    public function getTypeCode(){
        return isset(self::getTypeNames()[$this->type]) ? self::getTypeNames()[$this->type] : '';
    }

}
