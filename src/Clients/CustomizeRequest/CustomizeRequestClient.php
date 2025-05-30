<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CustomizeRequest;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Managers\ConfigManager;

final readonly class CustomizeRequestClient implements ClientInterface
{
    public function __construct(
        private ClientInterface $client,
        private ConfigManager $configManager,
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $config = $this->configManager->get(CustomizeRequestConfig::class, $request->getUri()->getHost());
        return $this->client->sendRequest(($config->callback)($request));
    }
}
