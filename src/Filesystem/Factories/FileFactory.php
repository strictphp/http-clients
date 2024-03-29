<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Filesystem\Factories;

use Illuminate\Contracts\Filesystem\Filesystem;
use StrictPhp\HttpClients\Clients\Event\Entities\FileInfoEntity;
use StrictPhp\HttpClients\Filesystem\Contracts\FileContract;
use StrictPhp\HttpClients\Filesystem\Contracts\FileFactoryContract;
use StrictPhp\HttpClients\Filesystem\Wrappers\File;

final class FileFactory implements FileFactoryContract
{
    public function __construct(private readonly Filesystem $filesystem)
    {
    }

    public function create(FileInfoEntity $file, string $suffix = ''): FileContract
    {
        $this->filesystem->makeDirectory($file->path);

        return new File($this->filesystem->path($file->path) . DIRECTORY_SEPARATOR . $file->name . $suffix);
    }
}
