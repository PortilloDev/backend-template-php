<?php

namespace App\Shared\Infrastructure\Symfony\Http;

use App\Shared\Domain\Exception\ValidationException;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class ValidationError extends ValidationException
{
    #[OA\Property(property: 'errors', type: 'array', items: new OA\Items(properties: [
        new OA\Property(property: 'field', type: 'string'),
        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(type: 'string')),
    ]))]
    #[Groups(['expose'])]
    protected array $errors;
}
