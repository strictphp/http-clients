<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;

final class ClientsChain
{
    /**
     * @param iterable<ClientFactoryContract> $factories
     */
    public static function build(ClientInterface $client, iterable $factories): ClientInterface
    {
        foreach ($factories as $factory) {
            $client = $factory->create($client);
        }

        return $client;
    }
}
