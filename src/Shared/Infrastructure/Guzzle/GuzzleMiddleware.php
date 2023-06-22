<?php

namespace App\Shared\Infrastructure\Guzzle;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class GuzzleMiddleware
{
    private static array $maskedParameters = [];

    /**
     * Middleware that logs requests, responses, and errors using a message
     * formatter.
     *
     * @param LoggerInterface  $logger    logs messages
     * @param MessageFormatter $formatter formatter used to create message strings
     *
     * @return callable returns a function that accepts the next handler
     *
     * @throws \InvalidArgumentException
     */
    public static function log(LoggerInterface $logger, MessageFormatter $formatter, array $maskedParameters = []): callable
    {
        self::$maskedParameters = $maskedParameters;

        return static fn (callable $handler) => static fn (Request $request, array $options) => $handler($request, $options)->then(
            static function (Response $response) use ($logger, $request, $formatter) {
                $maskedRequest = $request;
                $requestContentType = $request->getHeaderLine('content-type');
                if ('' !== $requestContentType && '0' !== $requestContentType) {
                    if (false !== stripos($requestContentType, 'application/json')) {
                        $maskedRequest = self::maskFromJson($request);
                    } elseif (false !== stripos($requestContentType, 'text/xml')) {
                        $maskedRequest = self::maskFromXml($request);
                    } elseif (false !== stripos($requestContentType, 'application/x-www-form-urlencoded')) {
                        $maskedRequest = self::maskFromUrlEncode($request);
                    }
                }

                $maskedResponse = $response;
                $responseContentType = $response->getHeaderLine('content-type');
                if ('' !== $responseContentType && '0' !== $responseContentType) {
                    if (false !== stripos($responseContentType, 'application/json')
                        || false !== stripos($requestContentType, 'text/javascript')
                    ) {
                        $maskedResponse = self::maskFromJson($response);
                    } elseif (false !== stripos($responseContentType, 'text/xml')) {
                        $maskedResponse = self::maskFromXml($response);
                    }
                }

                assert($maskedRequest instanceof RequestInterface);
                assert($maskedResponse instanceof ResponseInterface);
                $message = $formatter->format($maskedRequest, $maskedResponse);
                $logger->info($message);

                return $response;
            },
            static function ($reason) use ($logger, $request, $formatter) {
                $response = $reason instanceof RequestException
                    ? $reason->getResponse()
                    : null;
                $message = $formatter->format($request, $response, $reason);
                $logger->notice($message);

                return Create::rejectionFor($reason);
            }
        );
    }

    public static function onStats(TransferStats $stats, LoggerInterface $logger): void
    {
        $handlerData = [
            'url' => $stats->getEffectiveUri(),
            'transferTime' => sprintf('%.5f', $stats->getTransferTime()),
            'namelookupTime' => sprintf('%.5f', $stats->getHandlerStat('namelookup_time')),
            'connectTime' => sprintf('%.5f', $stats->getHandlerStat('connect_time')),
            'pretransferTime' => sprintf('%.5f', $stats->getHandlerStat('pretransfer_time')),
        ];

        $logger->info('[HTTP Request]'.json_encode($handlerData, JSON_THROW_ON_ERROR));
    }

    /**
     * @throws \InvalidArgumentException
     */
    private static function maskFromJson(MessageInterface $request): MessageInterface
    {
        $body = (string) $request->getBody();

        if ('' === $body) {
            return $request;
        }

        $originalBody = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        if (!\is_array($originalBody)) {
            return $request->withBody(Utils::streamFor($originalBody));
        }

        $parameters = self::$maskedParameters;
        $newBody = array_replace_recursive(
            $originalBody,
            self::arrayIntersectAssocRecursive($parameters, $originalBody)
        );

        return $request->withBody(
            Utils::streamFor(\GuzzleHttp\json_encode(new \ArrayObject($newBody), JSON_PRETTY_PRINT))
        );
    }

    private static function arrayIntersectAssocRecursive(mixed $arr1, mixed $arr2): array
    {
        if (!\is_array($arr1) || !\is_array($arr2)) {
            return $arr1;
        }

        $commonkeys = array_intersect(array_keys($arr1), array_keys($arr2));
        $ret = [];
        foreach ($commonkeys as $key) {
            $ret[$key] = self::arrayIntersectAssocRecursive($arr1[$key], $arr2[$key]);
        }

        return $ret;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private static function maskFromXml(MessageInterface $message): MessageInterface
    {
        $originalBody = (string) $message->getBody();

        if ('' === $originalBody) {
            return $message;
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($originalBody);

        foreach (self::$maskedParameters as $maskedParameter => $maskedValue) {
            $tags = $dom->getElementsByTagName($maskedParameter);
            foreach ($tags as $node) {
                // @var \DOMElement $node
                $node->nodeValue = $maskedValue;
            }
        }

        return $message->withBody(Utils::streamFor($dom->saveXML()));
    }

    /**
     * @throws \InvalidArgumentException
     */
    private static function maskFromUrlEncode(MessageInterface $message): MessageInterface
    {
        $originalData = (string) $message->getBody();

        if ('' === $originalData) {
            return $message;
        }

        parse_str($originalData, $data);
        $parameters = self::$maskedParameters;

        $formattedData = array_replace_recursive(
            $data,
            self::arrayIntersectAssocRecursive($parameters, $data)
        );

        if (isset($formattedData['Data'])) {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->loadXML($formattedData['Data']);

            foreach (self::$maskedParameters as $maskedParameter => $maskedValue) {
                $tags = $dom->getElementsByTagName($maskedParameter);
                foreach ($tags as $node) {
                    // @var \DOMElement $node
                    $node->nodeValue = $maskedValue;
                }
            }

            $formattedData['Data'] = $dom->saveXML();
        }

        return $message->withBody(
            Utils::streamFor(http_build_query($formattedData))
        );
    }
}
