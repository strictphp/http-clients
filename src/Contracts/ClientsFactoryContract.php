<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Contracts;

use Psr\Http\Client\ClientInterface;

interface ClientsFactoryContract
{
    /**
     * @param iterable<ClientFactoryContract> $factories
     */
    public function create(?ClientInterface $client = null, iterable $factories = null): ClientInterface;
}
