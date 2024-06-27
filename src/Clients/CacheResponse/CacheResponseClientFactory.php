<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CacheResponse;

use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;
use StrictPhp\HttpClients\Managers\ConfigManager;
use StrictPhp\HttpClients\Services\CacheRequestService;

final class CacheResponseClientFactory implements ClientFactoryContract
{
    public function __construct(
        private readonly CacheRequestService $cacheRequestService,
        private readonly ConfigManager $configManager,
    ) {
    }

    public function create(ClientInterface $client): ClientInterface
    {
        return new CacheResponseClient($client, $this->cacheRequestService, $this->configManager);
    }
}
