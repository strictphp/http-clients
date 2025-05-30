<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CacheResponse;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Managers\ConfigManager;
use StrictPhp\HttpClients\Services\SerializableResponseService;

final readonly class CacheResponseClient implements ClientInterface
{
    public function __construct(
        private ClientInterface $client,
        private CacheInterface $cache,
        private SerializableResponseService $serializableResponseService,
        private ConfigManager $configManager,
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

        $response = $config->saveOnly
            ? null
            : $this->serializableResponseService->restore($key, $this->cache->get($key));

        if ($response instanceof ResponseInterface === false) {
            $response = $this->client->sendRequest($request);

            $this->cache->set(
                $key,
                $this->serializableResponseService->store($key, $response, $config->limitByte),
                $config->ttl === 0 ? null : $config->ttl,
            );
        }

        return $response;
    }
}
