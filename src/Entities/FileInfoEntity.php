<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Entities;

final class FileInfoEntity
{
    public function __construct(
        public string $path,
        public string $name,
    ) {
    }
}
