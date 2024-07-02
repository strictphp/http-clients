<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

use StrictPhp\HttpClients\Exceptions\InvalidStateException;

final class Host
{
    /**
     * @var array<non-empty-string, non-empty-string>
     */
    private static array $hosts = [];

    /**
     * @param non-empty-string $host
     * @return non-empty-string
     */
    public static function allSubdomains(string $host): string
    {
        if (array_key_exists($host, self::$hosts)) {
            return self::$hosts[$host];
        }

        $levels = explode('.', $host);
        if (count($levels) < 2) {
            throw new InvalidStateException(sprintf('Minimum levels for domain is 2. Not allowed: %s', $host));
        }

        return self::$hosts[$host] = '*.' . implode('.', array_slice($levels, -2));
    }
}
