<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

final class Time
{
    public static function micro(): float
    {
        return microtime(true);
    }

    public static function sleep(int $milliSeconds): void
    {
        usleep($milliSeconds * 1000);
    }
}
