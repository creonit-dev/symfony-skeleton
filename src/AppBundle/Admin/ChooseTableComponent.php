<?php

namespace AppBundle\Admin;

use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;
use Creonit\AdminBundle\Component\Scope\Scope;
use Creonit\AdminBundle\Component\Scope\TableRowScope;
use Creonit\AdminBundle\Component\TableComponent;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class ChooseTableComponent extends TableComponent
{
    protected $actives = [];

    protected $scopeName;

    /** @var  TableRowScope */
    protected $scope;

    public function prepareSchema()
    {
        parent::prepareSchema();
    }

    public function schema()
    {
        $this->scope = $this->getScope($this->scopeName);

        $this->setAction('choose', "function(options){
            var \$row = this.findRowById(options.rowId);
                \$row.toggleClass('success');
     
            this.request('choose', $.extend({key: options.key}, this.getQuery()), {state: \$row.hasClass('success')});
            if(this.parent){
                this.parent.loadData();
            }
            
        }");

        $this->setHandler('choose', function(ComponentRequest $request, ComponentResponse $response) {
            $entityClass = $this->scope->getEntity() . 'Query';
            $target = $entityClass::create()->findPk($request->query->get('key')) or $response->flushError('Объект не найден');
            $this->choose($request, $response, $target, $request->data->getBoolean('state'));
        });
    }

    protected function decorate(ComponentRequest $request, ComponentResponse $response, ParameterBag $data, $entity, Scope $scope, $relation, $relationValue, $level)
    {
        if ($scope->getName() == $this->scopeName) {
            if ($data->get('_key') == $request->query->get('value')) {
                $data->set('_row_class', 'success');
            } else {
                if (in_array($entity->getId(), $this->actives)) {
                    $data->set('_row_class', 'success');
                }
            }
        }
    }

    /**
     * @param mixed $scopeName
     */
    public function setScopeName($scopeName)
    {
        $this->scopeName = $scopeName;
    }


    public function applySchemaAnnotation($annotation)
    {
        switch ($annotation['key']) {
            case 'scope':
                $this->setScopeName($annotation['value']);
                break;
            default:
                parent::applySchemaAnnotation($annotation);
        }
    }

    protected function loadData(ComponentRequest $request, ComponentResponse $response)
    {
        $this->actives = $this->actives($request, $response) ?: [];

        parent::loadData($request, $response);
    }


    /**
     * @param ComponentRequest $request
     * @param ComponentResponse $response
     * @param $target
     * @param bool $state
     * @return mixed
     */
    abstract protected function choose(ComponentRequest $request, ComponentResponse $response, $target, $state);

    /**
     * @param ComponentRequest $request
     * @param ComponentResponse $response
     * @return mixed
     */
    abstract protected function actives(ComponentRequest $request, ComponentResponse $response);
}