<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Contracts;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

interface CacheKeyMakerActionContract
{
    /**
     * @param array<string, array<string>|string> $headers
     */
    public function execute(array &$headers, StreamInterface $body, UriInterface $uri): void;
}
