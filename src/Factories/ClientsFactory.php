<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Factories;

use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;
use StrictPhp\HttpClients\Helpers\ClientsChain;

final class ClientsFactory
{
    /**
     * @param iterable<ClientFactoryContract> $factories
     */
    public function __construct(
        private readonly ClientInterface $client,
        private readonly iterable $factories = [],
    ) {
    }

    /**
     * @param iterable<ClientFactoryContract> $factories
     */
    public function create(iterable $factories = null): ClientInterface
    {
        return ClientsChain::build($this->client, $factories ?? $this->factories);
    }
}
