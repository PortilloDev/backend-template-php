<?php

namespace App\User\Domain\Contracts;

use App\User\Domain\Model\User;

interface PasswordHasherInterface
{
    public function hashPassword(User $user): void;
}
