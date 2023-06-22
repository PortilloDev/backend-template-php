<?php

namespace App\Comic\Infrastructure\Symfony\Controller;

use App\Comic\Application\Query\ListComics\ListComicsQuery;
use App\Comic\Infrastructure\Symfony\Http\ComicCollection;
use App\Shared\Domain\Pagination\Page;
use App\Shared\Infrastructure\Symfony\Controller\AbstractApiController;
use App\Shared\Infrastructure\Symfony\Http\ValidationError;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

class ComicListController extends AbstractApiController
{
    #[OA\Get(
        operationId: 'comicList',
        summary: 'Returns a comic list.',
        parameters: [
            new OA\Parameter(name: 'publisher', in: 'query', required: false, schema: new OA\Schema(type: 'string'), example: 'marvel'),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'count', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(ref: new Model(type: ComicCollection::class))
            ),
            new OA\Response(
                response: 400,
                description: 'Bad Request',
                content: new OA\JsonContent(ref: new Model(type: ValidationError::class, groups: ['expose']), format: 'application/json')
            ),
            new OA\Response(response: 500, description: 'Internal Server Error'),
        ]
    )]
    #[OA\Tag(name: 'Comics')]
    #[Route(path: '/comics', methods: 'GET')]
    public function __invoke(
        #[MapQueryParameter] string $publisher = null,
        #[MapQueryParameter] ?int $page = 1,
        #[MapQueryParameter] ?int $count = 10,
    ): JsonResponse {
        $results = $this->handleMessage(
            new ListComicsQuery(page: new Page($page, $count), publisher: $publisher)
        );

        return $this->success(new ComicCollection($results));
    }
}
