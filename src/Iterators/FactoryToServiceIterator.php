<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Iterators;

use Generator;
use IteratorAggregate;
use Psr\Container\ContainerInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;

/**
 * @implements IteratorAggregate<int, ClientFactoryContract>
 */
final class FactoryToServiceIterator implements IteratorAggregate
{
    /**
     * @param array<class-string<ClientFactoryContract>> $factories
     */
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly array $factories,
    ) {
    }

    /**
     * @return Generator<ClientFactoryContract>
     */
    public function getIterator(): Generator
    {
        foreach (new ReverseIterator($this->factories) as $factoryStr) {
            $factory = $this->container->get($factoryStr);
            assert($factory instanceof ClientFactoryContract);

            yield $factory;
        }
    }
}
