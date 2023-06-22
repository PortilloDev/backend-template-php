<?php

namespace App\User\Domain\Contracts;

use App\User\Domain\Model\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function findUserByEmail(string $email): ?User;
}
