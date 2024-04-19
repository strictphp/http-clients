<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Retry;

use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;
use StrictPhp\HttpClients\Managers\ConfigManager;

final class RetryClientFactory implements ClientFactoryContract
{
    public function __construct(
        private readonly ConfigManager $configManager,
    )
    {
    }

    public function create(ClientInterface $client): ClientInterface
    {
        return new RetryClient($client, $this->configManager);
    }
}