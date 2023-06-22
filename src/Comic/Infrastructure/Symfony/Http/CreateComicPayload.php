<?php

namespace App\Comic\Infrastructure\Symfony\Http;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateComicPayload
{
    public function __construct(
        #[OA\Property(type: 'string')]
        #[Assert\NotBlank]
        #[Assert\Type(type: 'string')]
        public string $title,
        #[OA\Property(type: 'string', example: 'Marvel')]
        #[Assert\NotBlank]
        #[Assert\Type(type: 'string')]
        public string $publisher,
    ) {
    }
}
