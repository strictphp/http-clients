<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Transformers;

use StrictPhp\HttpClients\Entities\FileInfoEntity;
use StrictPhp\HttpClients\Exceptions\InvalidStateException;

final class CacheKeyToFileInfoTransformer
{
    public function __construct(
        private readonly string $tempDir = '',
    ) {
    }

    public function transform(string $key, string $extension): FileInfoEntity
    {
        if (strlen($key) < 2) {
            throw new InvalidStateException('Cache key must be at least 2 characters long.');
        }

        preg_match('~^(?<path>.+/)(?<filename>.+)$~', $key, $path);

        if (isset($path['filename'], $path['path'])) {
            $subDir = $path['path'];
            $fileName = $path['filename'];
        } else {
            $subDir = '';
            $fileName = $key;
        }

        $subDir .= substr($fileName, 0, 2);

        return new FileInfoEntity($this->tempDir . '/' . $subDir, $fileName, $extension);
    }
}
