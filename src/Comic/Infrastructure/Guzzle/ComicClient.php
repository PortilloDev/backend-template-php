<?php

namespace App\Comic\Infrastructure\Guzzle;

use App\Shared\Infrastructure\Guzzle\AbstractRestClient;

class ComicClient extends AbstractRestClient implements ComicClientInterface
{
    public function getOptions(): array
    {
        return [];
    }
}
