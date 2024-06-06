<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Sleep;

use StrictPhp\HttpClients\Contracts\ConfigContract;

final class Config implements ConfigContract
{
    public function __construct(
        public readonly int $from = 500, // milliseconds
        public readonly int $to = 1000, // milliseconds
    ) {
    }

    public function initFromDefaultConfig($object): void
    {
        // intentionally empty
    }
}
