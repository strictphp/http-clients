<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Tests;

use Illuminate\Contracts\Filesystem\Filesystem;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Actions\FindExtensionFromHeadersAction;
use StrictPhp\HttpClients\Contracts\FindExtensionFromHeadersActionContract;
use StrictPhp\HttpClients\Filesystem\Contracts\FileFactoryContract;
use StrictPhp\HttpClients\Filesystem\Factories\FileFactory;
use StrictPhp\HttpClients\Services\CachePsr16Service;
use StrictPhp\HttpClients\Services\FilesystemService;
use StrictPhp\HttpClients\Transformers\CacheKeyToFileInfoTransformer;

final class Factories
{
    public static function createFilesystem(): Filesystem
    {
        return new FilesystemService(__DIR__ . '/temp/');
    }

    public static function createFindExtensionFromHeadersAction(): FindExtensionFromHeadersActionContract
    {
        return new FindExtensionFromHeadersAction();
    }

    public static function createFileFactory(?Filesystem $filesystem = null): FileFactoryContract
    {
        return new FileFactory($filesystem ?? self::createFilesystem());
    }

    public static function createCacheKeyToFileInfoTransformer(string $path): CacheKeyToFileInfoTransformer
    {
        return new CacheKeyToFileInfoTransformer($path);
    }

    public static function createCache(
        string $path,
        ?CacheKeyToFileInfoTransformer $cacheKeyToFileInfoTransformer = null,
        ?FileFactoryContract $fileFactory = null,
    ): CacheInterface {
        $fileFactory ??= self::createFileFactory();
        $transformer = $cacheKeyToFileInfoTransformer ?? self::createCacheKeyToFileInfoTransformer($path);

        return new CachePsr16Service($fileFactory, $transformer);
    }
}
