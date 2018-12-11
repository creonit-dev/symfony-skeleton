<?php

namespace AppBundle\Admin;

use Creonit\AdminBundle\Module;

abstract class ExtraAdminModule extends Module
{
    public function hasComponent($name)
    {
        if (parent::hasComponent($name)) {
            return true;
        }

        $namespace = (new \ReflectionClass($this))->getNamespaceName();
        $componentClass = $namespace . '\\' . $name;

        if (class_exists($componentClass)) {
            $this->addComponent(new $componentClass);
            return true;
        }

        return false;
    }
}