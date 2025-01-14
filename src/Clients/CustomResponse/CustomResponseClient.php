<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CustomResponse;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Helpers\ResponseFactory;
use StrictPhp\HttpClients\Managers\ConfigManager;

class CustomResponseClient implements ClientInterface
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly ConfigManager $configManager,
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $config = $this->configManager->get(CustomResponseConfig::class, $request->getUri()->getHost());
        if ($config->enabled === false) {
            $this->client->sendRequest($request);
        }

        return ResponseFactory::fromContent($config->content, $request);
    }
}
