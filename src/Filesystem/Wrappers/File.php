<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Filesystem\Wrappers;

use Psr\Http\Message\StreamInterface;
use StrictPhp\HttpClients\Filesystem\Interfaces\FileInterface;
use StrictPhp\HttpClients\Helpers\Byte;
use StrictPhp\HttpClients\Helpers\Stream;

final class File implements FileInterface
{
    private int $flag = 0;

    public function __construct(
        private readonly string $pathname,
    ) {
    }

    public function write(string|StreamInterface $content): void
    {
        if ($content instanceof StreamInterface) {
            $this->remove();
            Stream::rewind($content);

            while ($content->eof() === false) {
                $this->filePutContents($content->read(Byte::fromMega(1)), FILE_APPEND);
            }
        } else {
            $this->filePutContents($content, $this->flag);
            $this->flag = FILE_APPEND;
        }
    }

    public function content(): ?string
    {
        if ($this->isFile() === false) {
            return null;
        } elseif ($this->isTllExpired()) {
            $this->remove();
            return null;
        }

        $content = file_get_contents($this->pathname);

        return $content === false ? null : $content;
    }

    public function remove(): void
    {
        if ($this->isFile()) {
            unlink($this->pathname);
        }

        $ttlFile = $this->ttlFile();
        if (is_file($ttlFile)) {
            unlink($ttlFile);
        }
    }

    public function setTtl(?int $ttlToSeconds): void
    {
        if ($ttlToSeconds === null) {
            return;
        }

        file_put_contents($this->ttlFile(), (string) (time() + $ttlToSeconds));
    }

    public function getPathname(): string
    {
        return $this->pathname;
    }

    public function isFile(): bool
    {
        return is_file($this->pathname);
    }

    private function filePutContents(string $content, int $flags = 0): void
    {
        file_put_contents($this->pathname, $content, $flags);
    }

    private function ttlFile(): string
    {
        return $this->pathname . '.ttl';
    }

    private function isTllExpired(): bool
    {
        if (is_file($this->ttlFile()) === false) {
            return false;
        }
        $time = (int) file_get_contents($this->ttlFile());

        return $time < time();
    }
}
