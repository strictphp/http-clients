<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Services\CacheRequestService;
use StrictPhp\HttpClients\Services\LoadCustomFileService;

final class ResponseFactory
{
    /**
     * @param string|ResponseInterface|(callable(RequestInterface): ResponseInterface)|CacheRequestService $content
     */
    public static function fromContent($content, RequestInterface $request): ResponseInterface
    {
        if (is_callable($content)) {
            /** @var callable(RequestInterface): ResponseInterface $content */
            return ($content)($request);
        } elseif (is_string($content) && is_file($content)) { // *.shttp
            $cache = new CacheRequestService(new LoadCustomFileService($content));
            $body = $cache->restore('');
        } elseif ($content instanceof CacheRequestService) {
            $body = $content->restore('');
        } else {
            $body = $content;
        }

        return $body instanceof ResponseInterface
            ? $body
            : new Response(body: (string) $body);
    }
}
