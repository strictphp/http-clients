<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Event;

use StrictPhp\HttpClients\Contracts\ConfigContract;

final class Config implements ConfigContract
{
    public function __construct(
        public bool $enabled = true,
    )
    {
    }

    public function initFromDefaultConfig(ConfigContract $object): void
    {
    }
}
