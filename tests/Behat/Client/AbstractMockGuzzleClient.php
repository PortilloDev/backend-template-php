<?php

namespace App\Tests\Behat\Client;

use App\Shared\Domain\Contracts\RestClientInterface;
use App\Shared\Domain\Exception\ResourceException;
use App\Shared\Infrastructure\Guzzle\AbstractRestClient;
use App\Tests\Behat\Persistence\BehatVariablesDatabase;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractMockGuzzleClient extends AbstractRestClient implements RestClientInterface
{
    abstract public static function getName(): string;

    protected function call(string $method, string $endpoint, array $params = []): ResponseInterface
    {
        $this->logger->debug(
            sprintf(
                "\n\n-------- Mock HTTP request -----------\n\n%s\n\n%s\n-------------------\n",
                static::getName().': '.$method.' '.$endpoint,
                json_encode($params, JSON_PRETTY_PRINT)
            )
        );
        $hash = md5($method.$endpoint);
        if ($exception = BehatVariablesDatabase::get(static::getName().'exception'.$hash)) {
            throw new ResourceException(json_encode(['message' => $exception], JSON_THROW_ON_ERROR) ?: $exception);
        }

        BehatVariablesDatabase::set(static::getName().$hash.'sent', $params);
        if (!$data = BehatVariablesDatabase::get(static::getName().$hash)) {
            throw new \LogicException(sprintf('No response for url %s with method %s', $endpoint, $method));
        }

        $body = json_encode($data, JSON_THROW_ON_ERROR);
        $this->logger->debug(
            sprintf(
                "\n\n-------- Mock HTTP response -----------\n\n%s\n-------------------\n",
                $body
            )
        );

        return new Response(200, [], $body);
    }

    public function getOptions(): array
    {
        return [];
    }
}
