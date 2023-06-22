<?php

namespace App\Shared\Infrastructure\Symfony\Controller;

use App\Shared\Domain\Bus\AsyncMessageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class AbstractApiController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function success(mixed $data = null, int $status = 200, array $headers = []): JsonResponse
    {
        assert($status >= 200 && $status < 300, 'invalid success status code');

        return new JsonResponse(json_encode($data, JSON_PRETTY_PRINT), $status, $headers, true);
    }

    public function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return new JsonResponse($message, Response::HTTP_NOT_FOUND);
    }

    public function badRequest(string $message, array $errors = null, int $status = 400): JsonResponse
    {
        assert($status >= 400 && $status < 500, 'invalid bad request status code');

        return new JsonResponse($message, $status);
    }

    protected function handleMessage(object $message): mixed
    {
        if ($message instanceof AsyncMessageInterface) {
            $this->messageBus->dispatch($message);

            return 'dispatched';
        }

        return $this->handle($message);
    }
}
