<?php

namespace Creonit\RestBundle\Handler;

use Creonit\RestBundle\Exception\RestErrorException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RestError
{

    protected $message;
    protected $code = 0;
    protected $status = 400;
    public $query;
    public $request;
    public $files;

    public function __construct()
    {
        $this->query = new ParameterBag();
        $this->request = new ParameterBag();
        $this->files = new ParameterBag();
    }


    /**
     * @param string|null $message
     * @param int|null $code
     * @param int|null $status
     * @throws RestErrorException
     */
    public function send($message = null, $code = null, $status = null)
    {
        if (null !== $message) {
            $this->message = $message;
        }

        if (null !== $code) {
            $this->code = $code;
        }

        if (null !== $status) {
            $this->status = $status;
        }

        if ($this->has()) {
            $exception = new RestErrorException($this->message, $this->code);
            $exception->setRestError($this);
            throw $exception;
        }
    }

    public function unauthorized($message = '')
    {
        $this->send($message, 401, 401);
    }

    public function forbidden($message = '')
    {
        $this->send($message, 403, 403);
    }

    public function notFound($message = '')
    {
        $this->send($message, 404, 404);
    }

    /**
     * @return bool
     */
    public function has()
    {
        return ($this->message !== null || $this->request->count() || $this->query->count() || $this->files->count());
    }

    /**
     * @return array
     */
    public function dump()
    {
        $data = [
            'message' => (string)$this->message,
            'code' => (int)$this->code,
        ];

        if ($this->query->count()) {
            $data['query'] = $this->query->all();
        }

        if ($this->request->count()) {
            $data['request'] = $this->request->all();
        }

        if ($this->files->count()) {
            $data['files'] = $this->files->all();
        }

        return ['error' => $data];
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $code
     * @return RestError
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $message
     * @return RestError
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }


    public function set($path, $message)
    {
        list($scope, $key) = explode('/', $path);
        $this->{$scope}->set($key, $message);
        return $this;
    }
}
