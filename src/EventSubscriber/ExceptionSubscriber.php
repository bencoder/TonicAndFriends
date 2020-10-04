<?php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['handleException', 0],
            ],
        ];
    }

    public function handleException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $event->setResponse(
          new Response(json_encode([
            "status" => "error",
            "message" => $exception->getMessage()
          ]),
          $exception->getCode() ?: 500)
        );
    }
}