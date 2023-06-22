<?php

namespace App\User\Domain\Model;

class User
{
    public string $password = '';

    public function __construct(
        public string $id,
        public string $email,
        public string $plainPassword,
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
    ) {
    }
}
