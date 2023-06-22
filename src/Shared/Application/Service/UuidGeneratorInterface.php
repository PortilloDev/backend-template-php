<?php

namespace App\Shared\Application\Service;

interface UuidGeneratorInterface
{
    public function generate(): \Stringable;
}
