<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Store;

use StrictPhp\HttpClients\Contracts\ConfigContract;
use StrictPhp\HttpClients\Entities\AbstractConfig;

final class Config extends AbstractConfig
{
    public function __construct(
        public bool $enabled = true,
        public bool $serialized = true,
    )
    {
    }

    public function initFromDefaultConfig(ConfigContract $object): void
    {
    }
}
