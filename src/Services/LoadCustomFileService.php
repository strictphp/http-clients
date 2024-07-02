<?php

namespace StrictPhp\HttpClients\Services;

use DateInterval;
use Exception;
use Psr\SimpleCache\CacheInterface;

class LoadCustomFileService implements CacheInterface
{
    public function __construct(
        private readonly string|null $filepath,
    ) {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->filepath !== null && is_file($this->filepath)) {
            $content = file_get_contents($this->filepath);
            if (is_string($content)) {
                return $content;
            }

            return $default;
        }

        return $this->filepath ?? $default;
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        throw new Exception('not implemented');
    }

    public function delete(string $key): bool
    {
        throw new Exception('not implemented');
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
