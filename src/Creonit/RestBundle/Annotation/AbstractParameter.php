<?php

namespace Creonit\RestBundle\Annotation;

abstract class AbstractParameter
{
    public $name;
    public $description;
    public $type;
    public $format;
    public $required;
    public $in;

    public function __construct($data)
    {
        $this->name = $data['value'];
        $this->in = isset($data['in']) ? $data['in'] : false;
        $this->required = isset($data['required']) ? (bool) $data['required'] : false;
        $this->description = isset($data['description']) ? $data['description'] : '';
        $this->type = isset($data['type']) ? $data['type'] : 'string';
        $this->format = isset($data['format']) ? $data['format'] : '';
    }
}