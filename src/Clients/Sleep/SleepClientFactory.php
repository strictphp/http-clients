<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Sleep;

use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;

final class SleepClientFactory implements ClientFactoryContract
{
    public function create(ClientInterface $client): ClientInterface
    {
        return new SleepClient($client);
    }
}
