<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Services;

use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Responses\SerializableResponse;

class CacheRequestService
{
    public function __construct(
        private readonly CacheInterface $cache,
    ) {
    }

    public function restore(string $key): ?ResponseInterface
    {
        $response = $this->cache->get($key);

        if ($response === null) {
            return null;
        } elseif ($response instanceof SerializableResponse) {
            return $response->response;
        }

        $this->cache->delete($key);

        return null;
    }

    public function store(string $key, ResponseInterface $response, ?int $ttl): void
    {
        $this->cache->set($key, new SerializableResponse($response), $ttl);
    }
}
