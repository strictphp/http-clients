<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CacheResponse;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Managers\ConfigManager;
use StrictPhp\HttpClients\Services\CacheRequestService;

final class CacheResponseClient implements ClientInterface
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly CacheRequestService $cacheRequestService,
        private readonly ConfigManager $configManager,
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $config = $this->configManager->get(CacheResponseConfig::class, $request->getUri()->getHost());

        if ($config->enabled === false) {
            return $this->client->sendRequest($request);
        }

        $key = $config->getCacheKeyMakerAction()
            ->execute($request);
        $response = $config->saveOnly ? null : $this->cacheRequestService->restore($key);

        if ($response instanceof ResponseInterface === false) {
            $response = $this->client->sendRequest($request);
            $this->cacheRequestService->store($key, $response, $config->ttl === 0 ? null : $config->ttl);
        }

        return $response;
    }
}
