<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\MainHttp;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class MainHttpClient implements ClientInterface
{
    public function __construct(
        private readonly ClientInterface $client,
    )
    {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        // here you can start debugging, add breakpoint at line below
        return $this->client->sendRequest($request);
    }
}
