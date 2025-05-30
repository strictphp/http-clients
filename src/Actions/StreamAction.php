<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Actions;

use Psr\Http\Message\StreamInterface;
use StrictPhp\HttpClients\Contracts\StreamActionContract;
use StrictPhp\HttpClients\Filesystem\Interfaces\FileInterface;

final readonly class StreamAction implements StreamActionContract
{
    public function __construct(
        private int $buffer = 8192,
    )
    {
    }

    public function execute(StreamInterface $stream, FileInterface $file, ?int $buffer = null): void
    {
        if ($stream->isSeekable()) {
            $stream->rewind();
            while ($stream->eof() === false) {
                $file->write($stream->read($buffer ?? $this->buffer));
            }
            $stream->rewind();
        } else {
            $file->write((string) $stream);
        }
    }
}
