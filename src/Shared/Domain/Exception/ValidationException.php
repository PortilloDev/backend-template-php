<?php

namespace App\Shared\Domain\Exception;

class ValidationException extends DomainException implements \JsonSerializable
{
    protected array $errors = [];

    public function __construct(array $errors)
    {
        parent::__construct(sprintf('%s errors found', \count($errors)), 400);

        $this->errors = $errors;
    }

    public function jsonSerialize(): array
    {
        return [
            'errors' => $this->errors,
        ];
    }
}
