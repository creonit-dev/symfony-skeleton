<?php

namespace Creonit\RestBundle\Handler;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Validator\Constraint;

class RestHandler
{
    /** @var  ContainerInterface */
    protected $container;

    /** @var RestError */
    public $error;

    /** @var RestData */
    public $data;

    /** @var \Symfony\Component\Validator\Validator\ValidatorInterface */
    protected $validator;

    /** @var Request */
    protected $request;

    /**
     * Handler constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->error = new RestError();
        $this->data = new RestData();
        $this->validator = $container->get('validator');
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @param string|array $path
     * @param Constraint|Constraint[]|null $constraints
     * @param bool $sendError
     */
    public function validate($path, $constraints = null, $sendError = true)
    {
        if (is_array($path)) {
            foreach ($path as $scope => $keys) {
                foreach ($keys as $key => $constraints) {
                    $this->validate("{$scope}/{$key}", $constraints, false);
                }
            }

        } else {

            list($scope, $key) = explode('/', $path);

            foreach ($this->validator->validate($this->request->{$scope}->get($key), $constraints) as $violation) {
                $this->error->{$scope}->set($key, $violation->getMessage());
                break;
            }
        }

        if ($sendError) {
            $this->error->send();
        }
    }

    public function isValid($value, $constraints)
    {
        return !$this->validator->validate($value, $constraints)->count();
    }

    public function checkCsrfToken($id = 'common')
    {
        if (!$this->isCsrfTokenValid($id, $this->request->get('csrf_token'))) {
            $this->error->send('Произошла ошибка валидации CSRF токена');
        }
    }

    public function isCsrfTokenValid($id, $token)
    {
        return true;
        return $this->container->get('security.csrf.token_manager')->isTokenValid(new CsrfToken($id, $token));
    }

    public function checkFound($object, $message = 'Ресурс не найден')
    {
        if (empty($object)) {
            $this->error->notFound($message);
        }
    }

    public function checkAuthorization()
    {
        if (!$this->isAuthenticated()) {
            $this->error->unauthorized('Требуется авторизация');
        }
    }

    public function isAuthenticated()
    {
        return $this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    public function checkPermission($attributes, $object = null)
    {
        if (!$this->container->get('security.authorization_checker')->isGranted($attributes, $object)) {
            $this->error->forbidden('Доступ запрещен');
        }
    }

    public function response($data = null, $status = 200)
    {
        if (null !== $data) {
            $this->data->set($data);
        }

        $response = new Response($this->serialize(), $status);

        switch ($this->data->getFormat()) {
            case 'xml':
                $response->headers->set('Content-Type', 'application/xml');
                break;
            case 'yaml':
                $response->headers->set('Content-Type', 'text/yaml');
                break;
            case 'json':
                $response->headers->set('Content-Type', 'application/json');
                break;
            default:
                throw new \Exception('Unsupported format');
        }

        return $response;
    }

    public function normalize()
    {
        return $this->container->get('serializer')->normalize(
            $this->data->get(),
            $this->data->getFormat(),
            $this->data->context->all()
        );
    }

    public function serialize()
    {
        return $this->container->get('serializer')->serialize(
            $this->data->get(),
            $this->data->getFormat(),
            $this->data->context->all()
        );
    }
}