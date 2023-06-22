<?php

namespace App\User\Infrastructure\Symfony\Model;

use App\User\Domain\Model\User;
use OpenApi\Attributes as OA;

#[OA\Schema(properties: [
    new OA\Property(property: 'id', type: 'string'),
    new OA\Property(property: 'email', type: 'string'),
])]
readonly class UserInfo implements \JsonSerializable
{
    public function __construct(private User $user)
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->user->id,
            'email' => $this->user->email,
        ];
    }
}
