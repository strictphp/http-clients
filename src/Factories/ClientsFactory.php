<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Factories;

use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;
use StrictPhp\HttpClients\Contracts\ClientsFactoryContract;

final class ClientsFactory implements ClientsFactoryContract
{
    /**
     * @param iterable<ClientFactoryContract> $factories
     */
    public function __construct(
        private readonly ClientInterface $client,
        private readonly iterable $factories = [],
    ) {
    }

    public function create(?ClientInterface $client = null, iterable $factories = null): ClientInterface
    {
        $client ??= $this->client;
        foreach ($factories ?? $this->factories as $factory) {
            $client = $factory->create($client);
        }

        return $client;
    }
}
