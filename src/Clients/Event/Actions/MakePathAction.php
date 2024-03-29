<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Event\Actions;

use StrictPhp\HttpClients\Clients\Event\Entities\FileInfoEntity;
use StrictPhp\HttpClients\Clients\Event\Events\AbstractRequestEvent;
use StrictPhp\HttpClients\Contracts\MakePathActionContract;

final class MakePathAction implements MakePathActionContract
{
    public function execute(AbstractRequestEvent $event, string $extension = ''): FileInfoEntity
    {
        $uri = $event->request->getUri();
        $directory = implode('/', [
            $uri->getHost(),
            date('Y-m-d/H', (int) $event->start),
        ]);

        $filename = implode('-', [
                date('H-i-sO', (int) $event->start),
                $event->id,
            ]) . ".$extension";

        return new FileInfoEntity($directory, $filename);
    }
}
