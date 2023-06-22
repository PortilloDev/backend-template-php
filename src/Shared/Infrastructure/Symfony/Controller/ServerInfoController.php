<?php

namespace App\Shared\Infrastructure\Symfony\Controller;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ServerInfoController extends AbstractApiController
{
    #[OA\Get(
        operationId: 'serverInfo',
        summary: 'Retrieve basic server info.'
    )]
    #[OA\Tag('Info')]
    #[Route('/info', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $info = [
            'clientIP' => $request->getClientIp(),
            'clientIPs' => $request->getClientIps(),
            'headers' => $_SERVER,
        ];

        return $this->success($info);
    }
}
