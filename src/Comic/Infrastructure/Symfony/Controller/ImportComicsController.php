<?php

namespace App\Comic\Infrastructure\Symfony\Controller;

use App\Comic\Application\Command\ImportComics\ImportComicsCommand;
use App\Shared\Domain\Exception\ResourceException;
use App\Shared\Infrastructure\Symfony\Controller\AbstractApiController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ImportComicsController extends AbstractApiController
{
    #[OA\Patch(
        summary: 'Import comics from source.',
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 500, description: 'Internal Server Error'),
        ]
    )]
    #[OA\Tag(name: 'Comics')]
    #[Route(path: '/comics/import', methods: 'PATCH')]
    public function __invoke(): JsonResponse
    {
        try {
            return $this->success(
                $this->handleMessage(
                    new ImportComicsCommand()
                )
            );
        } catch (ResourceException $resourceException) {
            return $this->badRequest($resourceException);
        }
    }
}
