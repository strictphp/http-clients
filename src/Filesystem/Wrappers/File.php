<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Filesystem\Wrappers;

use SplFileObject;
use StrictPhp\HttpClients\Filesystem\Contracts\FileContract;

final class File implements FileContract
{
    private readonly SplFileObject $file;

    public function __construct(string $path)
    {
        $this->file = new SplFileObject($path, 'w');
    }

    public function write(string $content): void
    {
        $this->file->fwrite($content);
    }
}
