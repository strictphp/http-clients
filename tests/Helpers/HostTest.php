<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Tests\Helpers;

use Closure;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use StrictPhp\HttpClients\Exceptions\InvalidStateException;
use StrictPhp\HttpClients\Helpers\Host;

final class HostTest extends TestCase
{
    /**
     * @return array<string|int, array{0: Closure(static):void}>
     */
    public static function data(): array
    {
        return [
            '1LD' => [static function (self $self) {
                $self->assert(
                    new InvalidStateException('Minimum levels for domain is 2. Not allowed: com'),
                    'com',
                );
            }],
            '2LD' => [static function (self $self) {
                $self->assert('*.strictphp.com', 'strictphp.com');
            }],
            '3LD' => [static function (self $self) {
                $self->assert('*.strictphp.com', 'http-clients.strictphp.com');
            }],
            '4LD' => [static function (self $self) {
                $self->assert('*.strictphp.com', 'foo.http-clients.strictphp.com');
            }],
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

    /**
     * @param non-empty-string $host
     */
    public function assert(string|InvalidStateException $expected, string $host): void {
        if ($expected instanceof InvalidStateException) {
            $this->expectExceptionObject($expected);
        }

        Assert::assertSame($expected, Host::allSubdomains($host));
    }
}
