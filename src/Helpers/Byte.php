<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

final class Byte
{
    public const Mega = 1048576;

    public static function fromMega(int $megaByte): int
    {
        return $megaByte * self::Mega;
    }
}
