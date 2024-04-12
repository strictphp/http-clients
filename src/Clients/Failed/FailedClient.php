<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Failed;

use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class FailedClient implements ClientInterface
{
    public function __construct(
        public readonly string|ClientExceptionInterface $message = '',
        public readonly int $code = 0,
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if ($this->message instanceof ClientExceptionInterface) {
            throw $this->message;
        }

        throw new class($this->message, $this->code) extends Exception implements ClientExceptionInterface {
        };
    }
}
