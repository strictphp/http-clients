<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Filesystem\Interfaces;

use Psr\Http\Message\StreamInterface;

interface FileInterface
{
    public function write(string|StreamInterface $content): void;

    public function content(): ?string;

    public function remove(): void;

    public function setTtl(?int $ttlToSeconds): void;

    public function getPathname(): string;
}
