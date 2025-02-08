<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Event\Actions;

use StrictPhp\HttpClients\Clients\Event\Events\AbstractRequestEvent;
use StrictPhp\HttpClients\Contracts\MakePathActionContract;
use StrictPhp\HttpClients\Entities\FileInfoEntity;

final class MakePathAction implements MakePathActionContract
{
    public function execute(AbstractRequestEvent $event, string $extension = ''): FileInfoEntity
    {
        $uri = $event->request->getUri();
        $directory = implode('/', [
            date('Y-m-d', (int) $event->start),
            $uri->getHost(),
            date('H', (int) $event->start),
        ]);

        $filename = implode('-', [date('H-i-sO', (int) $event->start), $event->id]);

        return new FileInfoEntity($directory, $filename, $extension);
    }
}
