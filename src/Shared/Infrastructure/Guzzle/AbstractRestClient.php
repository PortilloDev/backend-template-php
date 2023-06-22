<?php

namespace App\Shared\Infrastructure\Guzzle;

use App\Shared\Domain\Contracts\RestClientInterface;
use App\Shared\Domain\Exception\ResourceException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractRestClient implements RestClientInterface
{
    private ?GuzzleClient $client = null;

    public function __construct(protected readonly LoggerInterface $logger)
    {
    }

    abstract public function getOptions(): array;

    /**
     * {@inheritdoc}
     */
    public function get(string $endpoint, array $params = []): ResponseInterface
    {
        return $this->call('GET', $endpoint, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function post(string $endpoint, array $params = []): ResponseInterface
    {
        return $this->call('POST', $endpoint, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function patch(string $endpoint, array $params = []): ResponseInterface
    {
        return $this->call('PATCH', $endpoint, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $endpoint, array $params = []): ResponseInterface
    {
        return $this->call('DELETE', $endpoint, $params);
    }

    /**
     * @throws GuzzleException
     * @throws ResourceException
     */
    protected function call(string $method, string $endpoint, array $params = []): ResponseInterface
    {
        $this->initialize();
        if (null === $this->client) {
            throw new \LogicException('Client not initialized');
        }

        try {
            if ('GET' === $method) {
                $response = $this->client->request($method, $endpoint, [
                    'query' => $params,
                ]);
            } else {
                $httpBody = Utils::jsonEncode($params);
                $response = $this->client->request($method, $endpoint, [
                    'body' => $httpBody,
                ]);
            }
        } catch (BadResponseException $badResponseException) {
            $message = $badResponseException->getResponse()->getBody();
            throw new ResourceException($message, $badResponseException->getCode(), $badResponseException);
        }

        $response->getBody()->rewind();

        return $response;
    }

    protected function getRequestHandler(): HandlerStack
    {
        $handlerStack = HandlerStack::create();
        $handlerStack->setHandler(new CurlHandler());
        $handlerStack->push(GuzzleMiddleware::log(
            $this->logger,
            new MessageFormatter(
                '
{url}
>>>>>>>>
{request}
<<<<<<<<
{response}
'
            )
        ));

        return $handlerStack;
    }

    private function initialize(): void
    {
        $options = $this->getOptions();
        $options['handler'] = $this->getRequestHandler();
        $this->client = new GuzzleClient($options);
    }
}
