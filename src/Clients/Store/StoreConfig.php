<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Store;

use StrictPhp\HttpClients\Entities\AbstractConfig;

final class StoreConfig extends AbstractConfig
{
    public function __construct(
        public bool $enabled = true,
        public bool $serialized = true,
        public bool $onFail = true,
        public bool $onSuccess = true,
    ) {
    }
}
