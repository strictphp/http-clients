<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Event;

use StrictPhp\HttpClients\Contracts\ConfigContract;
use StrictPhp\HttpClients\Entities\AbstractConfig;

final class Config extends AbstractConfig
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
