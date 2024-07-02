<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Actions;

use Psr\Http\Message\StreamInterface;
use StrictPhp\HttpClients\Contracts\StreamActionContract;
use StrictPhp\HttpClients\Filesystem\Contracts\FileInterface;

final class StreamAction implements StreamActionContract
{
    public function execute(StreamInterface $stream, FileInterface $file, int $buffer): void
    {
        if ($stream->isSeekable()) {
            $stream->rewind();
            while ($stream->eof() === false) {
                $file->write($stream->read($buffer));
            }
            $stream->rewind();
        } else {
            $file->write((string) $stream);
        }
    }
}
