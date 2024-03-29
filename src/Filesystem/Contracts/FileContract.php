<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Filesystem\Contracts;

interface FileContract
{
    public function write(string $content): void;
}
