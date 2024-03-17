<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Sleep;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Helpers\Time;

final class SleepClient implements ClientInterface
{
    /**
     * @var array<string, float>
     */
    private array $timeout = [];

    public function __construct(
        private readonly ClientInterface $client,
        private readonly int $from = 500,
        private readonly int $to = 1000,
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $host = $request->getUri()->getHost();
        if (isset($this->timeout[$host])) {
            $diff = Time::micro() - $this->timeout[$host];
            if ($diff < 1) {
                Time::sleep(random_int($this->from, $this->to));
            }
        }

        try {
            $response = $this->client->sendRequest($request);
        } finally {
            $this->timeout[$host] = Time::micro();
        }

        return $response;
    }
}
