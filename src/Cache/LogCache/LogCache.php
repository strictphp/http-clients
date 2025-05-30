<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Cache\LogCache;

use DateInterval;
use Generator;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

final readonly class LogCache implements CacheInterface
{
    public function __construct(
        private CacheInterface $cache,
        private LoggerInterface $logger,
        private bool $showData = false,
    ) {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->log('get', $key, [
            'default' => $default,
        ]);

        return $this->cache->get($key, $default);
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $this->log('set', $key, [
            'value' => $value,
            'ttl' => $ttl,
        ]);

        return $this->cache->set($key, $value, $ttl);
    }

    public function delete(string $key): bool
    {
        $this->log('del', $key);

        return $this->cache->delete($key);
    }

    public function clear(): bool
    {
        $this->log('clear');

        return $this->cache->clear();
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $this->log('mget', $keys, [
            'default' => $default,
        ]);

        return $this->cache->getMultiple($keys, $default);
    }

    /**
     * @param iterable<mixed> $values
     */
    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        $this->log('mset', self::extractKeys($values), [
            'value' => $values,
            'ttl' => $ttl,
        ]);

        return $this->cache->setMultiple($values, $ttl);
    }

    public function deleteMultiple(iterable $keys): bool
    {
        $this->log('mdel', $keys);

        return $this->cache->deleteMultiple($keys);
    }

    public function has(string $key): bool
    {
        $this->log('has', $key);

        return $this->cache->has($key);
    }

    /**
     * @param iterable<mixed> $data
     * @return Generator<string>
     */
    private static function extractKeys(iterable $data): Generator
    {
        foreach ($data as $key => $value) {
            /** @var string|int $key */
            yield (string) $key;
        }
    }

    /**
     * @param string|iterable<string> $keys
     * @param array<string, mixed> $data
     */
    private function log(string $action, string|iterable $keys = '', array $data = []): void
    {
        if (is_iterable($keys)) {
            $allKeys = [];
            foreach ($keys as $item) {
                $allKeys[] = $item;
            }
            $strKey = implode(', ', $allKeys);
        } else {
            $allKeys = $keys;
            $strKey = $keys;
        }

        $context = [
            'action' => $action,
            'key' => $allKeys,
        ];
        if ($this->showData) {
            $context += $data;
        }

        $this->logger->debug(sprintf('strictphp: cache %s %s', $action, substr($strKey, 0, 25)), $context);
    }
}
