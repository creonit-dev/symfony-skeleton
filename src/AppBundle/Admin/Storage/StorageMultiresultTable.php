<?php

namespace AppBundle\Admin\Storage;


use AppBundle\Model\Base\StorageResultQuery;
use AppBundle\Model\StorageResult;
use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;
use Creonit\AdminBundle\Component\Scope\Scope;
use Creonit\AdminBundle\Component\TableComponent;
use Propel\Runtime\ActiveQuery\Criteria;

class StorageMultiresultTable extends TableComponent
{

    /**
     * @title Коллекция элементов
     * @header
     * {{ button('Добавить элемент', {size: 'sm', type: 'success', icon: 'puzzle-piece'}) | open('StorageFillEditor' , {storage_id: _query.key}) }}
     *
     * @cols Название, .
     *
     * \StorageResult
     * @field title {load: 'entity.getTitle()'}
     * @sortable true
     *
     *
     * @col {{ (title | icon('puzzle-piece') | open('StorageFillEditor', {key: _key, storage_id: _query.key })) | controls }}
     * @col {{ buttons(_visible() ~ _delete()) }}
     *
     */
    public function schema()
    {

        $this->addHandler('_delete', function (ComponentRequest $request, ComponentResponse $response){
            $query = $request->query;
            if($query->has('key') and $query->has('scope') and $this->hasScope($query->get('scope'))){
                $scope = $this->getScope($query->get('scope'));

                /** @var StorageResult $entity **/
                if($entity = $scope->createQuery()->findPk($query->get('key'))){
                    $entity->delete();
                    $this->container->get('app.storage')->clearStorageDataCache($entity->getStorage()->getCode());
                    $response->data->set('success', true);

                }else{
                    $response->flushError('Элемент не найден');
                }

            }else{
                $response->flushError('Ошибка при выполнения запроса');
            }
        });

        $this->addHandler('_visible', function (ComponentRequest $request, ComponentResponse $response){
            $query = $request->query;
            if(
                $request->data->has('visible')
                and $query->get('key')
                and $scope = $query->get('scope')
                and $this->hasScope($scope)
            ){
                $scope = $this->getScope($scope);

                /** @var StorageResult $entity **/
                if($entity = $scope->createQuery()->findPk($query->get('key'))){
                    $visible = $request->data->getBoolean('visible');
                    $visibleField = $this->createField('visible');
                    $visibleField->save($entity, $visible);
                    $entity->save();

                    $this->container->get('app.storage')->clearStorageDataCache($entity->getStorage()->getCode());

                    $response->data->set('success', true);
                    $response->data->set('visible', $visibleField->load($entity));

                }else{
                    $response->flushError('Элемент не найден');
                }

            }else{
                $response->flushError('Ошибка при выполнения запроса');
            }
        });

        $this->addHandler('_sort', function (ComponentRequest $request, ComponentResponse $response){
            $query = $request->query;
            if($query->has('key') and $query->has('scope') and $this->hasScope($query->get('scope'))){
                $scope = $this->getScope($query->get('scope'));

                /** @var StorageResult $entity **/
                if($entity = $scope->createQuery()->findPk($query->get('key'))){
                    if($request->data->get('prev') and $prev = $scope->createQuery()->findPk($request->data->get('prev'))){
                        $entity->moveToRank($entity->getRank() > $prev->getRank() ? $prev->getRank()+1 : $prev->getRank());
                        $entity->moveToRank($entity->getRank() > $prev->getRank() ? $prev->getRank()+1 : $prev->getRank());
                    }else{
                        $entity->moveToTop();
                    }

                    $this->container->get('app.storage')->clearStorageDataCache($entity->getStorage()->getCode());

                    $response->data->set('success', true);

                }else{
                    $response->flushError('Элемент не найден');
                }

            }else{
                $response->flushError('Ошибка при выполнения запроса');
            }
        });
    }

    /**
     * @param ComponentRequest $request
     * @param ComponentResponse $response
     * @param StorageResultQuery $query
     * @param Scope $scope
     * @param $relation
     * @param $relationValue
     * @param $level
     */
    public function filter(ComponentRequest $request, ComponentResponse $response, $query, Scope $scope, $relation, $relationValue, $level)
    {
        $query->filterByStorageId($request->query->get('key'));
    }
}
