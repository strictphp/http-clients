<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Contracts;

use Psr\Http\Message\StreamInterface;
use StrictPhp\HttpClients\Filesystem\Contracts\FileContract;

interface StreamActionContract
{
    public function execute(StreamInterface $stream, FileContract $file, int $buffer): void;
}
