<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

use Psr\Http\Message\StreamInterface;
use StrictPhp\HttpClients\Filesystem\Contracts\FileContract;

final class Stream
{
    public static function fileWrite(StreamInterface $stream, FileContract $file, int $buffer): void
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

    public static function content(StreamInterface $stream): string
    {
        $body = (string) $stream;
        if ($stream->isSeekable()) {
            $stream->rewind();
        }
        return $body;
    }
}
