<?php

namespace App\Shared\Infrastructure\Symfony\Subscriber;

use App\Shared\Infrastructure\Symfony\Http\ValidationError;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class TransformExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface $logger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
        }
        if (($previous = $exception->getPrevious()) && $previous instanceof ValidationFailedException) {
            $errors = [];
            foreach ($previous->getViolations() as $violation) {
                if ($violation->getPropertyPath()) {
                    $errors[$violation->getPropertyPath()][] = $violation->getMessage();
                }
            }
            $exception = new ValidationError($errors);
            $code = $exception->getCode();
            $body = $this->serializer->serialize($exception, 'json');
        }

        $event->setResponse(
            JsonResponse::fromJsonString($body ?? $exception->getMessage(), $code ?? 400)
        );
        $this->logger->error($exception->getMessage(), $exception->getTrace());
    }
}
