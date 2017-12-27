<?php

namespace AppBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RestListener
{

    /** @var  ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event){
        if(!$event->isMasterRequest()) {
            return;
        }
    }

    public function onKernelResponse(FilterResponseEvent $event){
        if(!$event->isMasterRequest()) {
            return;
        }

        if(!preg_match('#/api/#', $event->getRequest()->getPathInfo())) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE');
        $response->headers->set('Access-Control-Allow-Headers', 'X-Header-One,X-Header-Two');
    }

    public function onKernelException(GetResponseForExceptionEvent $event){
        if(!$event->isMasterRequest()) {
            return;
        }

        if(!preg_match('#/api/#', $event->getRequest()->getPathInfo())) {
            return;
        }

        $exception = $event->getException();

        if($exception instanceof NotFoundHttpException){
            $response = new JsonResponse(['error' => $exception->getMessage()], 404);
            $event->setResponse($response);
        }else{
            $response = new JsonResponse(['error' => $exception->getMessage()], 500);
            $event->setResponse($response);
        }

    }

}