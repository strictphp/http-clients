<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Store;

use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;
use StrictPhp\HttpClients\Managers\ConfigManager;
use StrictPhp\HttpClients\Requests\SaveForPhpstormRequest;

final class StoreClientFactory implements ClientFactoryContract
{
    public function __construct(
        private readonly SaveForPhpstormRequest $saveForPhpstormRequest,
        private readonly ConfigManager $configManager,
    ) {
    }

    public function create(ClientInterface $client): ClientInterface
    {
        return new StoreClient($this->saveForPhpstormRequest, $this->configManager, $client);
    }
}
