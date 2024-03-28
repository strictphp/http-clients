<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CacheResponse\Actions;

use Psr\Http\Message\RequestInterface;
use StrictPhp\HttpClients\Contracts\CacheKeyMakerActionContract;

final class CacheKeyMakerAction implements CacheKeyMakerActionContract
{
    public function execute(RequestInterface $request): string
    {
        $headers = $request->getHeaders();
        $body = $request->getBody();

        foreach ($headers as $name => $header) {
            if (str_starts_with(strtolower($name), 'x-')) {
                unset($headers[$name]);
            }
        }

        return md5(serialize([
            $request->getMethod(),
            $request->getProtocolVersion(),
            $headers,
            (string) $body,
            (string) $request->getUri(),
        ]));
    }
}
