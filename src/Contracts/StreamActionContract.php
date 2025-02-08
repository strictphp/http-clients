<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Contracts;

use Psr\Http\Message\StreamInterface;
use StrictPhp\HttpClients\Filesystem\Interfaces\FileInterface;

interface StreamActionContract
{
    /**
     * @param positive-int|null $buffer
     */
    public function execute(StreamInterface $stream, FileInterface $file, ?int $buffer = null): void;
}
