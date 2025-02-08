<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Tests\Filesystem\Wrappers;

use Closure;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use StrictPhp\HttpClients\Filesystem\Wrappers\File;

final class FileTest extends TestCase
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

    public function assert(): void {
        $pathName = __DIR__ . '/../../temp/test-file.txt';
        @unlink($pathName);
        $file = new File($pathName);
        Assert::assertSame($pathName, $file->getPathname());
        Assert::assertNull($file->content());
        $file->write('Lorem');
        Assert::assertSame('Lorem', $file->content());
        $file->write(Utils::streamFor('Ipsum'));
        Assert::assertSame('Ipsum', $file->content());
        $file->setTtl(1);
        sleep(2);
        Assert::assertNull($file->content());
    }
}
