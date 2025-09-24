<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CacheResponse;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Helpers\Time;
use StrictPhp\HttpClients\Managers\ConfigManager;
use StrictPhp\HttpClients\Services\RelativeDateToTtlService;
use StrictPhp\HttpClients\Services\SerializableResponseService;

final readonly class CacheResponseClient implements ClientInterface
{
    public function __construct(
        private ClientInterface $client,
        private CacheInterface $cache,
        private SerializableResponseService $serializableResponseService,
        private ConfigManager $configManager,
        private RelativeDateToTtlService $relativeDateToTtlService,
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
                $this->resolveTtl($config->ttl),
            );
        }

        return $response;
    }

    private function resolveTtl(int|string $ttl): int
    {
        if ($ttl === '') {
            return 0;
        }

        if (is_string($ttl) && is_numeric($ttl)) {
            $ttl = (int) $ttl;
        }

        if (is_int($ttl)) {
            return $ttl;
        }

        if ($ttl === CacheResponseConfig::TtlEndOfDay) {
            return $this->relativeDateToTtlService->midnight();
        } elseif (Time::matchTime($ttl)) {
            return $this->relativeDateToTtlService->time($ttl);
        }

        return $this->relativeDateToTtlService->extend($ttl);
    }
}
