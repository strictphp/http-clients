<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Entities;

final class FileInfoEntity
{
    public string $name;

    public function __construct(
        public string $path,
        string $name,
        string $extension,
    ) {
        if ($extension !== '') {
            $name .= '.' . $extension;
        }

        $this->name = $name;
    }
}
