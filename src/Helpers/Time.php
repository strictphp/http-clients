<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

final class Time
{
    public static function seconds(): float
    {
        return microtime(true);
    }

    public static function milli(): float
    {
        return self::seconds() * 1000;
    }

    public static function sleep(int $milliSeconds): void
    {
        if ($milliSeconds > 0) {
            usleep($milliSeconds * 1000);
        }
    }
}
