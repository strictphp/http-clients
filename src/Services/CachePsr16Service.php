<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Services;

use DateInterval;
use Exception;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Entities\FileInfoEntity;
use StrictPhp\HttpClients\Filesystem\Contracts\FileFactoryContract;
use StrictPhp\HttpClients\Filesystem\Contracts\FileInterface;
use StrictPhp\HttpClients\Helpers\Time;

final class CachePsr16Service implements CacheInterface
{
    public function __construct(
        private readonly FileFactoryContract $fileFactory,
        private readonly string $tempDir = '',
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
        preg_match('~^(?<path>.+/)(?<filename>.+)$~', $key, $path);

        if (isset($path['filename'], $path['path'])) {
            $subDir = $path['path'];
            $fileName = $path['filename'];
        } else {
            $subDir = '';
            $fileName = $key;
        }

        $subDir .= substr($fileName, 0, 2);
        $fileInfo = new FileInfoEntity($this->tempDir . '/' . $subDir, $fileName);

        return $this->fileFactory->create($fileInfo, '.shttp');
    }
}
