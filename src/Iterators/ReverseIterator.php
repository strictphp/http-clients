<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Iterators;

use Generator;
use IteratorAggregate;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;

/**
 * @template T
 * @implements IteratorAggregate<int, ClientFactoryContract>
 */
final class ReverseIterator implements IteratorAggregate
{
    /**
     * @param array<T> $factories
     */
    public function __construct(
        private readonly array $factories,
    )
    {
    }

    /**
     * @return Generator<T>
     */
    public function getIterator(): Generator
    {
        $count = count($this->factories) - 1;

        for ($i = $count; $i >= 0; --$i) {
            yield $this->factories[$i];
        }
    }
}
