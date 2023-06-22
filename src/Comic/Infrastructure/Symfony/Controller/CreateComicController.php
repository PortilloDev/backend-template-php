<?php

namespace App\Comic\Infrastructure\Symfony\Controller;

use App\Comic\Application\Command\CreateComic\CreateComicCommand;
use App\Comic\Infrastructure\Symfony\Http\CreateComicPayload;
use App\Shared\Domain\Exception\ResourceException;
use App\Shared\Infrastructure\Symfony\Controller\AbstractApiController;
use App\Shared\Infrastructure\Symfony\Http\ValidationError;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class CreateComicController extends AbstractApiController
{
    #[OA\Post(
        summary: 'Create a new comic.',
        requestBody: new OA\RequestBody(
            description: 'Request content',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: CreateComicPayload::class))
        ),
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(
                response: 400,
                description: 'Bad Request',
                content: new OA\JsonContent(ref: new Model(type: ValidationError::class, groups: ['expose']), format: 'application/json')
            ),
            new OA\Response(response: 500, description: 'Internal Server Error'),
        ]
    )]
    #[OA\Tag(name: 'Comics')]
    #[Route(path: '/comics', methods: 'POST')]
    public function __invoke(#[MapRequestPayload(acceptFormat: 'json')] CreateComicPayload $payload): JsonResponse
    {
        try {
            return $this->success(
                $this->handleMessage(
                    new CreateComicCommand(title: $payload->title, publisher: $payload->publisher)
                )
            );
        } catch (ResourceException $resourceException) {
            return $this->badRequest($resourceException);
        }
    }
}
