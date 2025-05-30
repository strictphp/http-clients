<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CustomizeRequest;

use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;
use StrictPhp\HttpClients\Managers\ConfigManager;

final readonly class CustomizeRequestClientFactory implements ClientFactoryContract
{
    public function __construct(
        private ConfigManager $configManager,
    ) {
    }

    public function create(ClientInterface $client): ClientInterface
    {
        return new CustomizeRequestClient($client, $this->configManager);
    }
}
