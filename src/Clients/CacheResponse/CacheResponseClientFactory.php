<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CacheResponse;

use Psr\Http\Client\ClientInterface;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;
use StrictPhp\HttpClients\Managers\ConfigManager;
use StrictPhp\HttpClients\Services\SerializableResponseService;

final class CacheResponseClientFactory implements ClientFactoryContract
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly SerializableResponseService $serializableResponseService,
        private readonly ConfigManager $configManager,
    ) {
    }

    public function create(ClientInterface $client): ClientInterface
    {
        return new CacheResponseClient($client, $this->cache, $this->serializableResponseService, $this->configManager);
    }
}
