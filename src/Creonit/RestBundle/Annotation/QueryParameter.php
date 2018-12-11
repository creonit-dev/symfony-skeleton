<?php

namespace Creonit\RestBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;


/**
 * @Annotation
 * @Target({"METHOD"})
 */
class QueryParameter extends AbstractParameter
{
    public function __construct($data)
    {
        $data['in'] = 'query';
        parent::__construct($data);
    }
}