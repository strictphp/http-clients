<?php
declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CacheResponse;

use Psr\Http\Client\ClientInterface;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Contracts\CacheKeyMakerActionContract;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;

final class CacheResponseClientFactory implements ClientFactoryContract
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly ?CacheKeyMakerActionContract $cacheKeyMakerAction = null,
        private readonly bool $saveOnly = false,
        private readonly ?int $ttl = null,
    ) {
    }

    public function create(ClientInterface $client): ClientInterface
    {
        return new CacheResponseClient($client, $this->cache, $this->cacheKeyMakerAction, $this->saveOnly, $this->ttl);
    }

}
