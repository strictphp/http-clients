<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;

final class EventClientFactory implements ClientFactoryContract
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function create(ClientInterface $client): ClientInterface
    {
        return new EventClient($client, $this->eventDispatcher);
    }
}
