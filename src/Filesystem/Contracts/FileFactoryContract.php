<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Filesystem\Contracts;

use StrictPhp\HttpClients\Entities\FileInfoEntity;

interface FileFactoryContract
{
    public function create(FileInfoEntity $file, string $suffix = ''): FileInterface;
}
