<?php

namespace Creonit\RestBundle\EventListener;

use Creonit\RestBundle\Exception\RestErrorException;
use Creonit\RestBundle\Handler\RestError;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class RestListener
{

    /** @var  ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (!preg_match('#^/api/#', $event->getRequest()->getPathInfo())) {
            return;
        }
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (!preg_match('#^/api/#', $event->getRequest()->getPathInfo())) {
            return;
        }

        if ($this->container->get('request_stack')->getCurrentRequest() !== $event->getRequest()) {
            return;
        }

        $exception = $event->getException();

        if ($exception instanceof RestErrorException) {
            $error = $exception->getError();
        } else {
            $error = new RestError();
            $error->setMessage(
                $this->container->getParameter('kernel.debug')
                    ? $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine() . ' | ' . $exception->getTraceAsString()
                    : 'Системная ошибка'
            );
            $error->setCode($exception->getCode());
            $error->setStatus(500);
        }

        $event->setResponse(new JsonResponse($error->dump(), $error->getStatus()));
    }

}