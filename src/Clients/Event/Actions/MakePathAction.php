<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Event\Actions;

use Illuminate\Contracts\Filesystem\Filesystem;
use StrictPhp\HttpClients\Clients\Event\Events\AbstractRequestEvent;
use StrictPhp\HttpClients\Contracts\MakePathActionContract;

final class MakePathAction implements MakePathActionContract
{
    public function __construct(private readonly Filesystem $filesystem)
    {
    }

    public function execute(AbstractRequestEvent $event, string $extension = ''): string
    {
        $uri = $event->request->getUri();
        $directory = implode('/', [
            $uri->getHost(),
            date('Y-m-d/H', (int) $event->start),
        ]);
        $this->filesystem->makeDirectory($directory);

        $filename = implode('-', [
                date('H-i-sO', (int) $event->start),
                $event->id,
            ]) . ".$extension";

        return $this->filesystem->get($directory) . "/$filename";
    }
}
