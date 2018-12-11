<?php

namespace Creonit\RestBundle\Exception;


use Throwable;

/**
 * Class RestInvalidParameterException
 * @package Creonit\RestBundle\Exception
 */
class RestInvalidParameterException extends \Exception
{
    private $propertyPath;

    /**
     * RestInvalidParameterException constructor.
     * @param string $message
     * @param string $propertyPath
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", string $propertyPath = "", int $code = 0, Throwable $previous = null)
    {
        $this->propertyPath = $propertyPath;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return $this->propertyPath;
    }
}