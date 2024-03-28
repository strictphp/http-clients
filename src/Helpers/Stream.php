<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

use Psr\Http\Message\StreamInterface;
use SplFileObject;

final class Stream
{
    public static function fileWrite(StreamInterface $stream, SplFileObject $splFileObject, int $buffer): void
    {
        if ($stream->isSeekable()) {
            $stream->rewind();
            while ($stream->eof() === false) {
                $splFileObject->fwrite($stream->read($buffer));
            }
            $stream->rewind();
        } else {
            $splFileObject->fwrite((string) $stream);
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
