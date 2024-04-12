<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Contracts;

use Psr\Http\Message\ResponseInterface;

interface FindExtensionFromHeadersActionContract
{
    public function execute(ResponseInterface $response): string;
}
