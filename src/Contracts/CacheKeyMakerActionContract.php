<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Contracts;

use Psr\Http\Message\RequestInterface;

interface CacheKeyMakerActionContract
{
    public function execute(RequestInterface $request): string;
}
