<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Filesystem\Wrappers;

use SplFileObject;
use StrictPhp\HttpClients\Filesystem\Contracts\FileInterface;

final class File implements FileInterface
{
    private ?SplFileObject $file = null;

    public function __construct(
        private readonly string $pathname,
    ) {
    }

    public function write(string $content): void
    {
        $this->getFile()
            ->fwrite($content);
    }

    public function content(): ?string
    {
        $file = $this->readFile();
        if (! $file instanceof SplFileObject) {
            return null;
        } elseif ($file->getMTime() !== $file->getCTime() && $file->getMTime() < time()) {
            $this->remove();
            return null;
        }

        $content = $file->fread($file->getSize());
        if ($content === false) {
            return null;
        }

        return $content;
    }

    public function remove(): void
    {
        $file = $this->readFile();
        if ($file instanceof SplFileObject) {
            unlink($file->getPathname());
            $this->file = null;
        }
    }

    public function setTtl(?int $ttlToSeconds): void
    {
        /** @var int $ctime - file exists! */
        $ctime = $this->getFile()
            ->getCTime();
        touch($this->pathname, $ctime + (int) $ttlToSeconds);
    }

    private function getFile(): SplFileObject
    {
        if (! $this->file instanceof SplFileObject) {
            if (is_file($this->pathname) === false) {
                touch($this->pathname);
            }
            $this->file = new SplFileObject($this->pathname, 'r+');
        }

        return $this->file;
    }

    private function readFile(): ?SplFileObject
    {
        return $this->file ?? (is_file($this->pathname) ? $this->getFile() : null);
    }
}
