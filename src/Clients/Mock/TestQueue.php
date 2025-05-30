<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Mock;

use StrictPhp\HttpClients\Exceptions\LogicException;

/**
 * @template T
 */
final class TestQueue
{
    /**
     * @param list<T> $data
     */
    public function __construct(
        private array $data,
    )
    {
    }

    /**
     * @return T
     */
    public function first(): mixed
    {
        if ($this->data === []) {
            throw new LogicException('Queue is empty');
        }

        return array_shift($this->data);
    }
}
