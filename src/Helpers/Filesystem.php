<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

final class Filesystem
{
    public static function makeDirectory(string $path, int $mode = 0o755): bool
    {
        return @mkdir($path, $mode, true);
    }
}
