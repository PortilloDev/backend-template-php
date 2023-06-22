<?php

namespace App\User\Infrastructure\Symfony\Security;

use App\User\Domain\Model\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class SecurityUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    public string $id;

    public string $email;

    public ?string $password;

    public function __construct(User $user)
    {
        $this->id = $user->id;
        $this->email = $user->email;
        $this->password = $user->password;
    }

    public function getRoles(): array
    {
        return [];
    }

    public function eraseCredentials(): void
    {
        $this->password = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
}
