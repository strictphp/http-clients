<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CustomResponse;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Managers\ConfigManager;
use StrictPhp\HttpClients\Services\CacheRequestService;
use StrictPhp\HttpClients\Services\LoadCustomFileService;

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

        if (is_callable($config->content)) {
            return ($config->content)($request);
        } elseif (is_string($config->content) && is_file($config->content)) { // *.shttp
            $cache = new CacheRequestService(new LoadCustomFileService($config->content));
            $body = $cache->restore('');
        } elseif ($config->content instanceof CacheRequestService) {
            $body = $config->content->restore('');
        } else {
            $body = $config->content;
        }

        return $body instanceof ResponseInterface
            ? $body
            : new Response(body: (string) $body);
    }
}
