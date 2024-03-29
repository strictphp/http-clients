<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Sleep;

use StrictPhp\HttpClients\Contracts\ConfigContract;

final class Config implements ConfigContract
{
    public function __construct(
        public readonly int $from = 500,
        public readonly int $to = 1000,
    ) {
    }

    public function initByDefault($object): void
    {
        // intentionally empty
    }
}
