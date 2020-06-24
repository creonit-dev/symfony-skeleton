<?php

namespace App\Event\EventSubscriber;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class JsonRequestConverterSubscriber
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'replaceJsonInRequest',
        ];
    }

    public function replaceJsonInRequest(ControllerEvent $event)
    {
        $request = $event->getRequest();

        if ($request->getContentType() != 'json' || !$request->getContent()) {
            return;
        }

        $data = json_decode($request->getContent(), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new BadRequestHttpException(sprintf('Invalid JSON body: %s', json_last_error_msg()));
        }

        $request->request->replace(is_array($data) ? $data : []);
    }
}
