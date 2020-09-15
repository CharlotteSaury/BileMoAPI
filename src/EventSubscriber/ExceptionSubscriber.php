<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        //dd($exception);
        
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $message = 'This route or resource does not exist';
            $status = $exception->getStatusCode();
        } elseif ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            $message = "This method is not allowed for this route";
            $status = $exception->getStatusCode();
        } elseif ($exception instanceof \Pagerfanta\Exception\OutOfRangeCurrentPageException) {
            $message = $exception->getMessage();
            $status = 404;
        } else {
            $message = $exception->getMessage();
            $status = $exception->getStatusCode();
        }

        $data = [
            'status' => $status,
            'message' => $message
        ];

        $response = new JsonResponse($data);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
