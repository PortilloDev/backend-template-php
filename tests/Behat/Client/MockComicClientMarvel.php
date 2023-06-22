<?php

namespace App\Tests\Behat\Client;

use App\Comic\Infrastructure\Guzzle\ComicClientInterface;

class MockComicClientMarvel extends AbstractMockGuzzleClient implements ComicClientInterface
{
    public static function getName(): string
    {
        return 'Marvel';
    }
}
