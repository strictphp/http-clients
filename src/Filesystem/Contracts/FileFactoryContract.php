<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Filesystem\Contracts;

use StrictPhp\HttpClients\Entities\FileInfoEntity;
use StrictPhp\HttpClients\Filesystem\Interfaces\FileInterface;

interface FileFactoryContract
{
    public function create(FileInfoEntity $file): FileInterface;
}
