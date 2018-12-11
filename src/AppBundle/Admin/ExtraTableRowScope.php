<?php

namespace AppBundle\Admin;

use Creonit\AdminBundle\Component\Scope\Scope;
use Creonit\AdminBundle\Component\Scope\TableRowScope;

class ExtraTableRowScope extends TableRowScope
{
    protected $forceIndependent;

    public function applySchemaAnnotation($annotation)
    {
        switch ($annotation['key']) {
            case 'independent':
                $this->forceIndependent = true;
                break;
            default:
                parent::applySchemaAnnotation($annotation);
        }
        return $this;
    }

    public function setDependency(Scope $targetScope)
    {
        parent::setDependency($targetScope);

        if ($this->forceIndependent) {
            $this->independent = true;
        }

        return $this;
    }


}