<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Retry;

use Psr\Http\Client\ClientExceptionInterface;
use StrictPhp\HttpClients\Entities\AbstractConfig;
use Throwable;

final class RetryConfig extends AbstractConfig
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
}
