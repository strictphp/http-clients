<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Retry;

use Psr\Http\Client\ClientExceptionInterface;
use StrictPhp\HttpClients\Contracts\ConfigContract;
use Throwable;

final class Config implements ConfigContract
{
    /**
     * @var callable(Throwable): bool
     * @readonly
     */
    public $isMyException;

    /**
     * @param positive-int $tries - 1 is disabled
     */
    public function __construct(
        public readonly int $tries = 2,
        ?callable $isMyException = null,
    ) {
        $this->isMyException = $isMyException ?? static fn (
            Throwable $e,
        ): bool => $e instanceof ClientExceptionInterface;
    }

    public function initFromDefaultConfig(ConfigContract $object): void
    {
        // intentionally empty
    }
}
