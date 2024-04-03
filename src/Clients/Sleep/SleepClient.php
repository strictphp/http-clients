<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Sleep;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Helpers\Time;
use StrictPhp\HttpClients\Managers\ConfigManager;

final class SleepClient implements ClientInterface
{
    /**
     * @var array<string, float>
     */
    private array $timeout = [];

    public function __construct(
        private readonly ClientInterface $client,
        private readonly ConfigManager $configManager,

    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $host = $request->getUri()->getHost();
        $config = $this->configManager->get(Config::class, $host);

        if ($config->to > 0 && isset($this->timeout[$host])) {
            $diff = Time::micro() - $this->timeout[$host];
            if ($diff < 1) {
                Time::sleep(random_int($config->from, $config->to));
            }
        }

        try {
            $response = $this->client->sendRequest($request);
        } finally {
            if ($config->to > 0) {
                $this->timeout[$host] = Time::micro();
            }
        }

        return $response;
    }
}
