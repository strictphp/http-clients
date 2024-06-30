<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Sleep;

use StrictPhp\HttpClients\Contracts\ConfigContract;
use StrictPhp\HttpClients\Exceptions\InvalidStateException;

final class Config implements ConfigContract
{
    /**
     * @param int<1, max> $from milliseconds
     * @param int<2, max> $to milliseconds
     */
    public function __construct(
        public readonly int $from = 500,
        public readonly int $to = 1000,
        public readonly bool $enabled = true,
    ) {
        if ($this->from >= $this->to) {
            throw new InvalidStateException('Parameter $from is higher than $to.');
        }
    }

    public function initFromDefaultConfig($object): void
    {
        // intentionally empty
    }
}
