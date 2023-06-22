<?php

namespace App\User\Infrastructure\Symfony\Controller;

use App\Shared\Infrastructure\Symfony\Controller\AbstractApiController;
use App\User\Application\Query\UserInfo\UserInfoQuery;
use App\User\Domain\Model\User;
use App\User\Infrastructure\Symfony\Model\UserInfo;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractApiController
{
    #[OA\Get(
        summary: 'Retrieve user info.',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(ref: new Model(type: UserInfo::class))
            ),
        ]
    )]
    #[OA\Tag('User')]
    #[Route('/user', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        if (null === $this->getUser()) {
            return $this->notFound('User not found');
        }

        $user = $this->handleMessage(new UserInfoQuery($this->getUser()->getUserIdentifier()));
        if (!$user instanceof User) {
            return $this->notFound('User not found');
        }

        return $this->success(new UserInfo($user));
    }
}
