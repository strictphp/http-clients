<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

final class Host
{
    /**
     * @var array<string, string>
     */
    private static array $hosts = [];

    public static function allSubdomains(string $host): string
    {
        if (array_key_exists($host, self::$hosts)) {
            return self::$hosts[$host];
        }

        return self::$hosts[$host] = '*.' . implode('.', array_slice(explode('.', $host), -2));
    }
}
