<?php

namespace App\Shared\Domain\Contracts;

use Psr\Http\Message\ResponseInterface;

interface RestClientInterface
{
    public function get(string $endpoint, array $params = []): ResponseInterface;

    public function post(string $endpoint, array $params = []): ResponseInterface;

    public function patch(string $endpoint, array $params = []): ResponseInterface;

    public function delete(string $endpoint, array $params = []): ResponseInterface;
}
