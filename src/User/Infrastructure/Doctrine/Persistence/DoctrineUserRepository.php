<?php

namespace App\User\Infrastructure\Doctrine\Persistence;

use App\User\Domain\Contracts\UserRepositoryInterface;
use App\User\Domain\Model\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class DoctrineUserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findUserByEmail(string $email): ?User
    {
        return parent::findOneBy(['email' => $email]);
    }
}
