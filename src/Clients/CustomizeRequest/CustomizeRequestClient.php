<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CustomizeRequest;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Managers\ConfigManager;

final class CustomizeRequestClient implements ClientInterface
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly ConfigManager $configManager,
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $config = $this->configManager->get(CustomizeRequestConfig::class, $request->getUri()->getHost());
        return $this->client->sendRequest(($config->callback)($request));
    }
}
