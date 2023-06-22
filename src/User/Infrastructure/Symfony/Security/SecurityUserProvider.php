<?php

namespace App\User\Infrastructure\Symfony\Security;

use App\User\Domain\Contracts\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final readonly class SecurityUserProvider implements UserProviderInterface
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function refreshUser(UserInterface $user): SecurityUser
    {
        if (!$user instanceof SecurityUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class %s', $user::class));
        }

        if (($userEntity = $this->userRepository->findUserByEmail($user->getUserIdentifier())) === null) {
            throw new UserNotFoundException(sprintf('No user found for "%s"', $user->getUserIdentifier()));
        }

        return new SecurityUser($userEntity);
    }

    public function supportsClass(string $class): bool
    {
        return SecurityUser::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        if (null === ($user = $this->userRepository->findUserByEmail($identifier))) {
            throw new UserNotFoundException(sprintf('No user found for "%s"', $identifier));
        }

        return new SecurityUser($user);
    }
}
