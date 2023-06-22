<?php

namespace App\Shared\Domain\Exception;

class ResourceException extends DomainException implements \JsonSerializable
{
    public function jsonSerialize(): array
    {
        return [
            json_decode((string) $this->message, null, 512, JSON_THROW_ON_ERROR),
        ];
    }
}
