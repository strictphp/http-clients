<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Tests\Transformers;

use Closure;
use Exception;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use StrictPhp\HttpClients\Exceptions\InvalidStateException;
use StrictPhp\HttpClients\Transformers\CacheKeyToFileInfoTransformer;

final class CacheKeyToFileInfoTransformerTest extends TestCase
{
    /**
     * @return array<string|int, array{0: Closure(static):void}>
     */
    public static function data(): array
    {
        return [
            [static function (self $self) {
                    $self->assert('/12/12.test', '12');
                }],
            [static function (self $self) {
                    $self->assert('/123456/78/789.test', '123456/789');
                }],
            [static function (self $self) {
                    $self->assert('tmp/123456/78/789.test', '123456/789', 'tmp');
                }],
            [
                static function (self $self) {
                    $self->assert(
                        new InvalidStateException('Cache key must be at least 2 characters long.'),
                        '1',
                        'tmp',
                    );
                },
            ],
        ];
    }

    /**
     * @param Closure(static):void $assert
     */
    #[DataProvider('data')]
    public function test(Closure $assert): void
    {
        $assert($this);
    }

    public function assert(string|Exception $expected, string $key, string $tempDir = ''): void
    {
        $transformer = new CacheKeyToFileInfoTransformer($tempDir);
        if ($expected instanceof Exception) {
            $this->expectExceptionObject($expected);
        }

        $fileInfo = $transformer->transform($key, 'test');

        Assert::assertSame($expected, $fileInfo->path . '/' . $fileInfo->name);
    }
}
