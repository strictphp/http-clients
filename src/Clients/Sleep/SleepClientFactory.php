<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Sleep;

use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;
use StrictPhp\HttpClients\Managers\ConfigManager;

final readonly class SleepClientFactory implements ClientFactoryContract
{
    public function __construct(
        private ConfigManager $configManager,
    ) {
    }

    public function create(ClientInterface $client): ClientInterface
    {
        return new SleepClient($client, $this->configManager);
    }
}
