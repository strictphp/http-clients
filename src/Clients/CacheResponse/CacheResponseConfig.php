<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CacheResponse;

use StrictPhp\HttpClients\Clients\CacheResponse\Actions\CacheKeyMakerAction;
use StrictPhp\HttpClients\Contracts\CacheKeyMakerActionContract;
use StrictPhp\HttpClients\Contracts\ConfigInterface;
use StrictPhp\HttpClients\Entities\AbstractConfig;

final class CacheResponseConfig extends AbstractConfig
{
    public function __construct(
        public int $ttl = 0,
        public bool $saveOnly = false,
        public bool $enabled = true,
        private ?CacheKeyMakerActionContract $cacheKeyMakerAction = null,
    ) {
    }

    public function initFromDefaultConfig(ConfigInterface $object): void
    {
        $this->cacheKeyMakerAction = $object->getCacheKeyMakerAction();
    }

    public function getCacheKeyMakerAction(): CacheKeyMakerActionContract
    {
        return $this->cacheKeyMakerAction ??= new CacheKeyMakerAction();
    }
}
