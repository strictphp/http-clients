<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Services;

use DateInterval;
use Exception;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Filesystem\Contracts\FileFactoryContract;
use StrictPhp\HttpClients\Filesystem\Interfaces\FileInterface;
use StrictPhp\HttpClients\Helpers\Time;
use StrictPhp\HttpClients\Transformers\CacheKeyToFileInfoTransformer;

final class CachePsr16Service implements CacheInterface
{
    public function __construct(
        private readonly FileFactoryContract $fileFactory,
        private readonly CacheKeyToFileInfoTransformer $cacheKeyToFileInfoTransformer,
    ) {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $content = $this->createFileInfoEntity($key)
            ->content();

        return $content === null ? $default : @unserialize($content);
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $file = $this->createFileInfoEntity($key);
        $file->write(serialize($value));
        $file->setTtl(Time::ttlToSeconds($ttl));

        return true;
    }

    public function delete(string $key): bool
    {
        $this->createFileInfoEntity($key)
            ->remove();

        return true;
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

    private function createFileInfoEntity(string $key): FileInterface
    {
        $fileInfo = $this->cacheKeyToFileInfoTransformer->transform($key, 'shttp');

        return $this->fileFactory->create($fileInfo);
    }
}
