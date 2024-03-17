<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Actions;

use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Contracts\FindExtensionFromHeadersActionContract;

class FindExtensionFromHeadersAction implements FindExtensionFromHeadersActionContract
{
    public function execute(ResponseInterface $response): string
    {
        $headers = $response->getHeader('Content-Type');

        foreach ($headers as $value) {
            $lower = strtolower($value);
            $extension = match (true) {
                str_contains($lower, 'json') => 'json',
                str_contains($lower, 'html') => 'html',
                str_contains($lower, 'pdf') => 'pdf',
                default => static::more($lower),
            };
            if ($extension !== null) {
                return $extension;
            }
        }

        return 'txt';
    }

    protected static function more(string $extension): ?string
    {
        return null;
    }
}
