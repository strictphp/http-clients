<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CacheResponse;

use Psr\Http\Client\ClientInterface;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;
use StrictPhp\HttpClients\Managers\ConfigManager;
use StrictPhp\HttpClients\Services\SerializableResponseService;

final readonly class CacheResponseClientFactory implements ClientFactoryContract
{
    public function __construct(
        private CacheInterface $cache,
        private SerializableResponseService $serializableResponseService,
        private ConfigManager $configManager,
    ) {
    }

    public function create(ClientInterface $client): ClientInterface
    {
        return new CacheResponseClient($client, $this->cache, $this->serializableResponseService, $this->configManager);
    }
}
