<?php

namespace Creonit\RestBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class RequestParameter extends AbstractParameter
{
    public function __construct($data)
    {
        $data['in'] = 'request';
        parent::__construct($data);
    }
}