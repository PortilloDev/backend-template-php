<?php

namespace App\User\Infrastructure\Symfony\Security;

use App\User\Domain\Contracts\PasswordHasherInterface;
use App\User\Domain\Model\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class PasswordHasher implements PasswordHasherInterface
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function hashPassword(User $user): void
    {
        $symfonyUser = new SecurityUser($user);
        $user->password = $this->hasher->hashPassword($symfonyUser, $user->plainPassword);
    }
}
