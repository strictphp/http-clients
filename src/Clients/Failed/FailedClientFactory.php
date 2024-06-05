<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Failed;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;

final class FailedClientFactory implements ClientFactoryContract
{
    public function __construct(
        private readonly string|ClientExceptionInterface $message = '',
        private readonly int $code = 0,
    ) {
    }

    public function create(ClientInterface $client): ClientInterface
    {
        return new FailedClient($this->message, $this->code);
    }
}
