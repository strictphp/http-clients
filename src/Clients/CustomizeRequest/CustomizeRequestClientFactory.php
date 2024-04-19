<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CustomizeRequest;

use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;
use StrictPhp\HttpClients\Managers\ConfigManager;

final class CustomizeRequestClientFactory implements ClientFactoryContract
{
    public function __construct(
        private readonly ConfigManager $configManager,
    ) {
    }

    public function create(ClientInterface $client): ClientInterface
    {
        return new CustomizeRequestClient($client, $this->configManager);
    }
}