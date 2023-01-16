<?php

namespace Kriss\Notification\Services;

use Kriss\Notification\Helper\JsonHelper;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class HttpClient
{
    protected ClientInterface $httpClient;
    protected RequestFactoryInterface $requestFactory;
    protected StreamFactoryInterface $streamFactory;
    protected Logger $logger;

    public function __construct(
        ClientInterface         $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface  $streamFactory,
        Logger                  $logger
    )
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->logger = $logger;
    }

    public function requestPostJson(string $url, array $jsonData): ResponseInterface
    {
        return $this->sendRequest(
            $this->requestFactory->createRequest('POST', $url)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(
                    $this->streamFactory->createStream(JsonHelper::encode($jsonData))
                )
        );
    }

    public function requestGet(string $url): ResponseInterface
    {
        return $this->sendRequest(
            $this->requestFactory->createRequest('GET', $url)
        );
    }

    private function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->logger->info(fn() => [
            'type' => 'request',
            'url' => (string)$request->getUri(),
            'method' => $request->getMethod(),
            'headers' => $request->getHeaders(),
            'body' => (string)$request->getBody(),
        ]);

        $start = microtime(true);
        $response = $this->httpClient->sendRequest($request);

        $this->logger->info(fn() => [
            'type' => 'response',
            'ts' => round(microtime(true) - $start, 6),
            'status' => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body' => (string)$response->getBody(),
        ]);

        return $response;
    }
}