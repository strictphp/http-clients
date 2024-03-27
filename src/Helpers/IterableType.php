<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

final class IterableType
{
    /**
     * @template T
     * @param iterable<T> $iterator
     *
     * @return array<T>
     */
    public static function reverse(iterable $iterator): array
    {
        return array_reverse(iterator_to_array($iterator));
    }
}
