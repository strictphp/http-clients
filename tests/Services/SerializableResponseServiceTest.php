<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Tests\Services;

use Closure;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use StrictPhp\HttpClients\Helpers\Byte;
use StrictPhp\HttpClients\Services\SerializableResponseService;
use StrictPhp\HttpClients\Tests\Factories;

final class SerializableResponseServiceTest extends TestCase
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
        $savedFile = __DIR__ . '/../temp/cacheService/test/super/du/duper.txt';
        @unlink($savedFile);
        $path = 'cacheService/test';
        $transformer = Factories::createCacheKeyToFileInfoTransformer($path);
        $fileFactory = Factories::createFileFactory();

        $service = new SerializableResponseService(
            $transformer,
            $fileFactory,
            Factories::createFindExtensionFromHeadersAction(),
        );

        $response = new Response(body: Utils::streamFor(str_repeat('R', Byte::fromMega(1) + 1)));
        $cacheKey = 'super/duper';
        Assert::assertSame('val', $service->restore($cacheKey, 'val'));
        $serializableResponse = $service->store($cacheKey, $response, 1);
        $responseCache = $service->restore($cacheKey, $serializableResponse);
        Assert::assertTrue($responseCache instanceof Response);
        Assert::assertTrue(is_file($savedFile));
        Assert::assertSame((string) $response->getBody(), (string) $responseCache->getBody());
    }
}
