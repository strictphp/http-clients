<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CacheResponse\Actions;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use StrictPhp\HttpClients\Contracts\CacheKeyMakerActionContract;

final class CacheKeyMakerAction implements CacheKeyMakerActionContract
{
    public function execute(array &$headers, StreamInterface $body, UriInterface $uri): void
    {
        foreach ($headers as $name => $header) {
            if (str_starts_with(strtolower($name), 'x-')) {
                unset($headers[$name]);
            }
        }
    }
}
