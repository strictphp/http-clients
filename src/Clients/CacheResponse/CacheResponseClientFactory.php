<?php
declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CacheResponse;

use Psr\Http\Client\ClientInterface;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;
use StrictPhp\HttpClients\Managers\ConfigManager;

final class CacheResponseClientFactory implements ClientFactoryContract
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly ?ConfigManager $configs = null,
    ) {
    }

    public function create(ClientInterface $client): ClientInterface
    {
        return new CacheResponseClient($client, $this->cache, $this->configs ?? new ConfigManager());
    }

}
