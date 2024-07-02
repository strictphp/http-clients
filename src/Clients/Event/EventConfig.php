<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Event;

use StrictPhp\HttpClients\Contracts\ConfigInterface;
use StrictPhp\HttpClients\Entities\AbstractConfig;

final class Config extends AbstractConfig
{
    public function __construct(
        public bool $enabled = true,
    )
    {
    }

    public function initFromDefaultConfig(ConfigInterface $object): void
    {
    }
}
