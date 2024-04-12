<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Contracts;

use Psr\Http\Client\ClientInterface;

interface ClientFactoryContract
{
    public function create(ClientInterface $client): ClientInterface;
}
