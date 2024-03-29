<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Factories;

use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;

final class ClientsFactory
{
    /**
     * @param iterable<ClientFactoryContract> $factories
     */
    public function __construct(
        private readonly ClientInterface $client,
        private iterable $factories = [],
    ) {
    }

    /**
     * @param iterable<ClientFactoryContract> $factories
     */
    public function create(iterable $factories = null): ClientInterface
    {
        $client = $this->client;
        foreach ($factories ?? $this->factories as $factory) {
            $client = $factory->create($client);
        }

        return $client;
    }
}
