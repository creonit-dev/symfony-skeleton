<?php

namespace Creonit\RestBundle\Exception;

use Creonit\RestBundle\Handler\RestError;

class RestErrorException extends \Exception
{
    /** @var RestError */
    protected $error;

    /**
     * @param RestError $error
     */
    public function setRestError(RestError $error)
    {
        $this->error = $error;
    }

    /**
     * @return RestError
     */
    public function getError()
    {
        return $this->error;
    }
}