<?php

namespace App\User\Application\Query\UserInfo;

use App\Shared\Domain\Bus\HandlerInterface;
use App\User\Domain\Contracts\UserRepositoryInterface;
use App\User\Domain\Model\User;

class UserInfoQueryHandler implements HandlerInterface
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function __invoke(UserInfoQuery $query): ?User
    {
        return $this->userRepository->findUserByEmail($query->username);
    }
}
