<?php

namespace Creonit\RestBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;


/**
 * @Annotation
 * @Target({"METHOD"})
 */
class PathParameter extends AbstractParameter
{
    public function __construct($data)
    {
        $data['in'] = 'path';
        parent::__construct($data);
    }
}