<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Store;

use StrictPhp\HttpClients\Contracts\ConfigInterface;
use StrictPhp\HttpClients\Entities\AbstractConfig;

final class StoreConfig extends AbstractConfig
{
    public function __construct(
        public bool $enabled = true,
        public bool $serialized = true,
    )
    {
    }

    public function initFromDefaultConfig(ConfigInterface $object): void
    {
    }
}
