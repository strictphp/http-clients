<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Services;

use DateInterval;
use Exception;
use Psr\SimpleCache\CacheInterface;

final class LoadCustomFileService implements CacheInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        if (is_file($key)) {
            $content = file_get_contents($key);
            return $content === false ? $default : @unserialize($content);
        }

        return $default;
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        throw new Exception('not implemented');
    }

    public function delete(string $key): bool
    {
        return is_file($key) && unlink($key);
    }

    public function clear(): bool
    {
        throw new Exception('not implemented');
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        throw new Exception('not implemented');
    }

    /**
     * @param iterable<mixed> $values
     */
    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        throw new Exception('not implemented');
    }

    public function deleteMultiple(iterable $keys): bool
    {
        throw new Exception('not implemented');
    }

    public function has(string $key): bool
    {
        throw new Exception('not implemented');
    }
}
