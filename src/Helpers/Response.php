<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Responses\SerializableResponse;
use StrictPhp\HttpClients\Services\LoadCustomFileService;

final class Response
{
    /**
     * @param string|ResponseInterface|(callable(RequestInterface): ResponseInterface) $content
     */
    public static function fromContent($content, RequestInterface $request): ResponseInterface
    {
        if (is_callable($content)) {
            /** @var callable(RequestInterface): ResponseInterface $content */
            return ($content)($request);
        } elseif (is_string($content) && is_file($content)) { // *.shttp
            $body = self::restore(new LoadCustomFileService(), $content);
        } else {
            $body = $content;
        }

        return $body instanceof ResponseInterface
            ? $body
            : new GuzzleResponse(body: $body);
    }

    public static function restore(CacheInterface $cache, string $key): ?ResponseInterface
    {
        $response = $cache->get($key);

        if ($response === null) {
            return null;
        } elseif ($response instanceof SerializableResponse) {
            return $response->response;
        }

        $cache->delete($key);

        return null;
    }
}
