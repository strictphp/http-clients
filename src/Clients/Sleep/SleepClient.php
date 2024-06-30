<?php declare(strict_types=1);

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
        $host = $request->getUri()
            ->getHost();
        $config = $this->configManager->get(Config::class, $host);

        if ($config->enabled === false) {
            return $this->client->sendRequest($request);
        }

        if (isset($this->timeout[$host])) {
            $diff = (int) (Time::milli() - $this->timeout[$host]);
            $sleep = random_int($config->from, $config->to);
            Time::sleep($sleep - $diff);
        }

        try {
            $response = $this->client->sendRequest($request);
        } finally {
            $this->timeout[$host] = Time::milli();
        }

        return $response;
    }
}
