<?php

namespace App\Shared\Infrastructure\Symfony;

use App\Shared\Application\Service\UuidGeneratorInterface;
use Symfony\Component\Uid\Factory\UuidFactory;

class UuidGenerator implements UuidGeneratorInterface
{
    public function __construct(private readonly UuidFactory $uuidFactory)
    {
    }

    public function generate(): \Stringable
    {
        return $this->uuidFactory->create();
    }
}
