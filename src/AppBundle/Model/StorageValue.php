<?php

namespace AppBundle\Model;

use AppBundle\Model\Base\StorageValue as BaseStorageValue;

/**
 * Skeleton subclass for representing a row from the 'storage_value' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class StorageValue extends BaseStorageValue
{

    public function getValue($params = []){
        $field = $this->getStorageField();
        $fieldValue = $this;
        if(isset($params['YouTubeId'])){
            return $this->getYouTubeId();
        }
        if($field->getTypeCode() == 'file') {
            $value = $fieldValue->getFile();
        }else if($field->getTypeCode() == 'image'){
            $value = $fieldValue->getImage();
        }else if($field->getTypeCode() == 'video'){
            $value = $fieldValue->getVideo();
        }else if($field->getTypeCode() == 'gallery'){
            $value = $fieldValue->getGalleryId() ? $fieldValue->getGallery()->getList() : [];
        }else if($field->getTypeCode() == 'select'){
            $option = $fieldValue->getStorageOption();
            $value = $option ? $option->getTitle() : null;
        }elseif($field->getTypeCode() == 'checkbox'){
            $value= $fieldValue->getBool();
        }elseif($field->getTypeCode() == 'product'){
            $value= $fieldValue->getProduct();
        }else{
            $value = $fieldValue->getText();
        }

        return $value;
    }

    public function getYouTubeId(){
        if($this->video_id){
            preg_match('/(?:\?v=([\w\d_-]+)|.be\/([\w\d_-]+))/i', $this->getVideo()->getUrl(), $match);
            return $match[1] ? $match[1] : $match[2];
        }

        return '';
    }

    public function setValue($value){
        $field = $this->getStorageField();
        $fieldValue = $this;

        if($field->getTypeCode() == 'file') {
            $fieldValue->setFile($value);
        }else if($field->getTypeCode() == 'image'){
            $fieldValue->setImage($value);
        }else if($field->getTypeCode() == 'gallery'){
            $fieldValue->setGallery($value);
        }else if($field->getTypeCode() == 'select'){
            $fieldValue->setStorageOption($value);
        }elseif($field->getTypeCode() == 'checkbox'){
            $fieldValue->setText(!!$value);
        }else{
            $fieldValue->setText($value);
        }

        $fieldValue->save();
    }

}
