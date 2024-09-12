<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Services;

use h4kuna\Serialize\Serialize;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Responses\SerializableResponse;

final class CacheRequestService
{
    public function __construct(
        private readonly CacheInterface $cache,
    ) {
    }

    public function restore(string $key): ?ResponseInterface
    {
        $data = $this->cache->get($key);

        if ($data === null) {
            return null;
        } elseif (is_string($data) === false) {
            $this->cache->delete($key);
            return null;
        }

        $response = Serialize::decode($data, self::class);
        if ($response instanceof SerializableResponse) {
            return $response->response;
        }

        $this->cache->delete($key);

        return null;
    }

    public function store(string $key, ResponseInterface $response, ?int $ttl): void
    {
        $this->cache->set($key, Serialize::encode(new SerializableResponse($response), self::class), $ttl);
    }
}
