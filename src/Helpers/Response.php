<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Exceptions\LogicException;
use StrictPhp\HttpClients\Responses\SerializableResponse;
use StrictPhp\HttpClients\Services\LoadCustomFileService;

/**
 * @phpstan-type callbackResponseType (callable(RequestInterface): ResponseInterface)
 * @phpstan-type inputType string|ResponseInterface|callbackResponseType
 */
final class Response
{
    private const FileExtension = '.' . SerializableResponse::FileExtension;

    /**
     * @param inputType $content
     */
    public static function fromContent($content, RequestInterface $request): ResponseInterface
    {
        if (is_callable($content)) {
            /** @var callbackResponseType $content */
            return ($content)($request);
        } elseif (is_string($content) && is_file($content)) {
            if (str_ends_with($content, self::FileExtension)) {
                $body = self::restore(new LoadCustomFileService(), $content);
            } else {
                $body = file_get_contents($content);
                if ($body === false) {
                    throw new LogicException('Failed to read file: ' . $content);
                }
                $body = rtrim($body);
            }
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
