<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Factories;

use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;
use StrictPhp\HttpClients\Helpers\ClientsChain;
use StrictPhp\HttpClients\Helpers\IterableType;

final class ClientsFactory
{
    /**
     * @var array<ClientFactoryContract>
     */
    private ?array $reverse = null;

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
        if ($factories === null) {
            if ($this->reverse === null) {
                $this->reverse = IterableType::reverse($this->factories);
                $this->factories = [];
            }
            $factories = $this->reverse;
        } else {
            $factories = IterableType::reverse($factories);
        }

        return ClientsChain::build($this->client, $factories);
    }
}
