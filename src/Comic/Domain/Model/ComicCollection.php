<?php

namespace App\Comic\Domain\Model;

use App\Shared\Domain\Collection;

class ComicCollection extends Collection
{
    public function type(): string
    {
        return Comic::class;
    }
}
