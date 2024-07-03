<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

use DateInterval;
use DateTimeImmutable;

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

    public static function ttlToSeconds(null|int|DateInterval $ttl = null): ?int
    {
        if ($ttl instanceof DateInterval) {
            return self::dateIntervalToSeconds($ttl);
        }

        return $ttl;
    }

    private static function dateIntervalToSeconds(DateInterval $dateInterval): int
    {
        $now = new DateTimeImmutable();
        $expiresAt = $now->add($dateInterval);

        return $expiresAt->getTimestamp() - $now->getTimestamp();
    }
}
