<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CacheResponse\Actions;

use Psr\Http\Message\RequestInterface;
use StrictPhp\HttpClients\Contracts\CacheKeyMakerActionContract;

final class CacheKeyMakerAction implements CacheKeyMakerActionContract
{
    public function execute(RequestInterface $request): string
    {
        $headers = $request->getHeaders();
        $body = $request->getBody();
        $uri = $request->getUri();

        foreach (array_keys($headers) as $name) {
            if (str_starts_with(strtolower($name), 'x-')) {
                unset($headers[$name]);
            }
        }

        return $uri->getHost() . '/' . sha1((string) json_encode([
            $request->getMethod(),
            $request->getProtocolVersion(),
            $headers,
            (string) $body,
            (string) $uri,
        ]));
    }
}
