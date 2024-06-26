<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Sleep;

use StrictPhp\HttpClients\Contracts\ConfigContract;

final class Config implements ConfigContract
{
    /**
     * @param int $from milliseconds
     * @param int $to milliseconds
     */
    public function __construct(
        public readonly int $from = 500,
        public readonly int $to = 1000,
    ) {
    }

    public function initFromDefaultConfig($object): void
    {
        // intentionally empty
    }
}
