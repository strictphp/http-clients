<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\MainHttp;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Helpers\Stream;

final readonly class MainHttpClient implements ClientInterface
{
    public function __construct(
        private ClientInterface $client,
    )
    {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        // here you can start debugging, add breakpoint at line below
        $response = $this->client->sendRequest($request);
        Stream::rewind($response->getBody());

        return $response;
    }
}
