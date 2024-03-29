<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

use Psr\Http\Message\StreamInterface;

final class Stream
{
    public static function content(StreamInterface $stream): string
    {
        $body = (string) $stream;
        if ($stream->isSeekable()) {
            $stream->rewind();
        }
        return $body;
    }
}
