<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Filesystem\Wrappers;

use SplFileObject;
use StrictPhp\HttpClients\Filesystem\Contracts\FileContract;

final class File implements FileContract
{
    private readonly SplFileObject $file;

    public function __construct(string $path)
    {
        touch($path);
        $this->file = new SplFileObject($path, 'r+');
    }

    public function write(string $content): void
    {
        $this->file->fwrite($content);
    }

    public function content(): ?string
    {
        $size = $this->file->getSize();

        if ($size === 0) {
            return null;
        }
        $content = $this->file->fread($size);
        if ($content === false) {
            return null;
        }

        return $content;
    }

    public function remove(): void
    {
        @unlink($this->file->getPathname());
    }
}
