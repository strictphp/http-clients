<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CacheResponse;

use StrictPhp\HttpClients\Clients\CacheResponse\Actions\CacheKeyMakerAction;
use StrictPhp\HttpClients\Contracts\CacheKeyMakerActionContract;
use StrictPhp\HttpClients\Contracts\ConfigContract;

final class Config implements ConfigContract
{
    /**
     * @param int|null $ttl - 0 disable saving, $saveOnly does not work
     */
    public function __construct(
        public ?int $ttl = 0,
        public bool $saveOnly = false,
        private ?CacheKeyMakerActionContract $cacheKeyMakerAction = null,
    ) {
    }

    public function initFromDefaultConfig(ConfigContract $object): void
    {
        $this->cacheKeyMakerAction = $object->getCacheKeyMakerAction();
    }

    public function getCacheKeyMakerAction(): CacheKeyMakerActionContract
    {
        return $this->cacheKeyMakerAction ??= new CacheKeyMakerAction();
    }

}
