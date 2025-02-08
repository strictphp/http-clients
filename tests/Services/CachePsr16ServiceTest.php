<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Tests\Services;

use Closure;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use StrictPhp\HttpClients\Tests\Factories;

final class CachePsr16ServiceTest extends TestCase
{
    /**
     * @return array<string|int, array{0: Closure(static):void}>
     */
    public static function data(): array
    {
        return [[static function (self $self) {
                    $self->assert();
                }], ];
    }

    /**
     * @param Closure(static):void $assert
     */
    #[DataProvider('data')]
    public function test(Closure $assert): void
    {
        $assert($this);
    }

    public function assert(): void
    {
        $cache = Factories::createCache('psr16service');

        $key = 'foo/bla';
        Assert::assertNull($cache->get($key));
        Assert::assertTrue($cache->set($key, 'lorem ipsum', 1));
        Assert::assertSame('lorem ipsum', $cache->get($key));
        $cache->delete($key);
    }
}
