<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CacheResponse;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Exceptions\InvalidStateException;
use StrictPhp\HttpClients\Managers\ConfigManager;
use StrictPhp\HttpClients\Responses\SerializableResponse;

final class CacheResponseClient implements ClientInterface
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly CacheInterface $cache,
        private readonly ConfigManager $configService,
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $config = $this->configService->get(Config::class, $request->getUri()->getHost());

        if ($config->ttl !== null && $config->ttl < 1) {
            return $this->request($request);
        }

        $key = $config->getCacheKeyMakerAction()->execute($request);
        $response = $config->saveOnly ? null : $this->restoreRequest($key);

        if ($response instanceof ResponseInterface === false) {
            $response = $this->request($request);
            $this->storeRequest($key, $response, $config->ttl);
        }

        return $response;
    }

    private function request(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }


    private function restoreRequest(string $key): ?ResponseInterface
    {
        $response = $this->cache->get($key);

        if (($response instanceof SerializableResponse) === false && $response !== null) {
            throw new InvalidStateException('The data in cache are broken.');
        }

        return $response?->response;
    }

    private function storeRequest(string $key, ResponseInterface $response, ?int $ttl): void
    {
        $this->cache->set($key, new SerializableResponse($response), $ttl);
    }
}
