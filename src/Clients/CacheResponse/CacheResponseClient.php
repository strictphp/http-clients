<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CacheResponse;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Clients\CacheResponse\Actions\CacheKeyMakerAction;
use StrictPhp\HttpClients\Contracts\CacheKeyMakerActionContract;
use StrictPhp\HttpClients\Exceptions\InvalidStateException;
use StrictPhp\HttpClients\Responses\SerializableResponse;

final class CacheResponseClient implements ClientInterface
{
    private readonly CacheKeyMakerActionContract $cacheKeyMakerAction;

    /**
     * @param int|null $ttl - 0 disable saving, $saveOnly does not work
     */
    public function __construct(
        private readonly ClientInterface $client,
        private readonly CacheInterface $cache,
        CacheKeyMakerActionContract $cacheKeyMakerAction = null,
        private readonly bool $saveOnly = false,
        private readonly ?int $ttl = null,
    ) {
        $this->cacheKeyMakerAction = $cacheKeyMakerAction ?? new CacheKeyMakerAction();
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if ($this->ttl !== null && $this->ttl < 1) {
            return $this->request($request);
        }

        $key = $this->cacheKeyMakerAction->execute($request);
        $response = $this->saveOnly ? null : $this->restoreRequest($key);

        if ($response instanceof ResponseInterface === false) {
            $response = $this->request($request);
            $this->storeRequest($key, $response);
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

    private function storeRequest(string $key, ResponseInterface $response): void
    {
        $this->cache->set($key, new SerializableResponse($response), $this->ttl);
    }
}
