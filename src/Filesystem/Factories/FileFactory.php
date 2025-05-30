<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Filesystem\Factories;

use Illuminate\Contracts\Filesystem\Filesystem;
use StrictPhp\HttpClients\Entities\FileInfoEntity;
use StrictPhp\HttpClients\Filesystem\Contracts\FileFactoryContract;
use StrictPhp\HttpClients\Filesystem\Interfaces\FileInterface;
use StrictPhp\HttpClients\Filesystem\Wrappers\File;

final readonly class FileFactory implements FileFactoryContract
{
    public function __construct(
        private Filesystem $filesystem,
    ) {
    }

    public function create(FileInfoEntity $file): FileInterface
    {
        $this->filesystem->makeDirectory($file->path);

        return new File($this->filesystem->path($file->path) . DIRECTORY_SEPARATOR . $file->name);
    }
}
