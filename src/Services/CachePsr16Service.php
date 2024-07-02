<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Services;

use DateInterval;
use Exception;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Clients\Event\Entities\FileInfoEntity;
use StrictPhp\HttpClients\Filesystem\Contracts\FileFactoryContract;
use StrictPhp\HttpClients\Filesystem\Contracts\FileInterface;

final class CachePsr16Service implements CacheInterface
{
    public function __construct(
        private readonly FileFactoryContract $fileFactory,
        private readonly string $tempDir = '',
    ) {
    }

    /**
     * @return ?string
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->createFileInfoEntity($key)
            ->content();
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        assert(is_string($value));
        $this->createFileInfoEntity($key)
            ->write($value);

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
        $subDir = implode('/', array_slice(str_split($key, 2), 0, 2));
        $fileInfo = new FileInfoEntity($this->tempDir . '/' . $subDir, $key);

        return $this->fileFactory->create($fileInfo, '.shttp');
    }
}
