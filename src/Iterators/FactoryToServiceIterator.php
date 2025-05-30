<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Iterators;

use Generator;
use IteratorAggregate;
use Psr\Container\ContainerInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;

/**
 * @implements IteratorAggregate<int, ClientFactoryContract>
 */
final readonly class FactoryToServiceIterator implements IteratorAggregate
{
    /**
     * @param ReverseIterator<int, class-string<ClientFactoryContract>> $factories
     */
    public function __construct(
        private ContainerInterface $container,
        private ReverseIterator $factories,
    ) {
    }

    /**
     * @return Generator<ClientFactoryContract>
     */
    public function getIterator(): Generator
    {
        foreach ($this->factories as $factoryStr) {
            $factory = $this->container->get($factoryStr);
            assert($factory instanceof ClientFactoryContract);

            yield $factory;
        }
    }
}
