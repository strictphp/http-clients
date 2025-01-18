<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Event;

use StrictPhp\HttpClients\Entities\AbstractConfig;

final class EventConfig extends AbstractConfig
{
    public function __construct(
        public bool $enabled = true,
    ) {
    }
}
