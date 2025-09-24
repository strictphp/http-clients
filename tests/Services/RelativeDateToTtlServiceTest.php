<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Tests\Services;

use DateTimeImmutable;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use StrictPhp\HttpClients\Exceptions\LogicException;
use StrictPhp\HttpClients\Services\RelativeDateToTtlService;
use Symfony\Component\Clock\MockClock;

final class RelativeDateToTtlServiceTest extends TestCase
{
    public function testRelativeDateToTtl(): void
    {
        $service = $this->givenRelativeDateToTtlService();

        Assert::assertSame(60 * 60 * 24, $service->time('8:01:02'));
        Assert::assertSame(1, $service->time('8:01:03'));
        Assert::assertSame(7138, $service->time('10:00'));

        Assert::assertSame(57538, $service->midnight());

        Assert::assertSame(3721, $service->extend('1 hour 2 minutes 1 seconds'));
        Assert::assertSame(1, $service->extend('-23 hours -59 minutes -59 seconds'));

        Assert::assertSame(57538, $service->extend('today')); // 2024-01-02 00:00:00 + 1 day
        Assert::assertSame(14338, $service->extend('noon')); // 2024-01-02 12:00:00
    }

    public function testExtendFailed(): void
    {
        $service = $this->givenRelativeDateToTtlService();
        try {
            $service->extend('-1 day');
            Assert::fail('Expected LogicException was not thrown.');
        } catch (LogicException $logicException) { // @phpstan-ignore catch.neverThrown
            Assert::assertSame('The relative time must be in the future.', $logicException->getMessage());
        }
    }

    public function testTimeFailed(): void
    {
        $service = $this->givenRelativeDateToTtlService();

        try {
            $service->time('lorem ipsum');
            Assert::fail('Expected LogicException was not thrown.');
        } catch (LogicException $logicException) { // @phpstan-ignore catch.neverThrown
            Assert::assertSame('Invalid time format. Use "H:i" or "H:i:s".', $logicException->getMessage());
        }
    }

    private function givenRelativeDateToTtlService(): RelativeDateToTtlService
    {
        return new RelativeDateToTtlService(new MockClock(new DateTimeImmutable('2024-01-02 08:01:02')));
    }
}
