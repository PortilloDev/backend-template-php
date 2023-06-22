<?php

namespace App\Shared\Domain\Bus;

interface DispatcherInterface
{
    public function dispatch(object $object): mixed;
}
