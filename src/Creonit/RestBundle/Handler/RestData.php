<?php

namespace Creonit\RestBundle\Handler;

use Symfony\Component\HttpFoundation\ParameterBag;

class RestData
{

    protected $data;
    protected $format = 'json';
    public $context;

    public function __construct()
    {
        $this->context = new ParameterBag();
    }

    public function set($data)
    {
        $this->data = $data;
        return $this;
    }

    public function get()
    {
        return $this->data;
    }

    /**
     * @param string $format
     * @return RestData
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param $groups
     * @return $this
     */
    public function setGroups($groups)
    {
        $this->context->set('groups', $groups);
        return $this;
    }

    /**
     * @param $group
     * @return $this
     */
    public function addGroup($group)
    {
        $groups = $this->context->get('groups');
        $groups[] = $group;
        $this->context->set('groups', $groups);
        return $this;
    }

}