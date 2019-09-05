<?php

namespace AppBundle\Model;

use AppBundle\Model\Base\StorageResult as BaseStorageResult;
use Propel\Runtime\ActiveQuery\Criteria;

/**
 * Skeleton subclass for representing a row from the 'storage_result' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class StorageResult extends BaseStorageResult
{

    public function getTitle() {
        $title = '#' . $this->getId();

        if($titleValue = StorageValueQuery::create()->filterByStorageResult($this)->useStorageFieldQuery()->filterByCode('title')->endUse()->findOne()) {
            if ($titleValue->getText()) {
                return $titleValue->getText();
            } else {
                return $title;
            }
        }

        if($fieldValue = StorageValueQuery::create()->filterByStorageResult($this)->filterByText('', Criteria::NOT_EQUAL)->findOne()) {
            if($fieldValue->getText()) {
                return $fieldValue->getText();
            } else {
                return $title;
            }
        }

        return $title;
    }

    public function getValue($field){
        if($fieldValue = StorageValueQuery::create()->filterByResultId($this->getId())->useStorageFieldQuery()->filterByCode($field)->endUse()->findOne()){
            return $fieldValue->getValue();
        }
    }

}
