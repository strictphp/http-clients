<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Contracts;

use StrictPhp\HttpClients\Clients\Event\Events\AbstractRequestEvent;

interface MakePathActionContract
{
    public function execute(AbstractRequestEvent $event, string $extension = ''): string;
}
