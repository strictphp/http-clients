<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Tests\Clients\Mock;

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use StrictPhp\HttpClients\Clients\Mock\MockClient;
use StrictPhp\HttpClients\Exceptions\LogicException;

final class MockClientTest extends TestCase
{
    public function testFilesInQueue(): void
    {
        $client = new MockClient([__DIR__ . '/fixtures/RES.200.1.shttp', __DIR__ . '/fixtures/RES.200.2.txt']);

        $request = new Request('GET', 'https://example.com/api/endpoint');

        $response1 = $client->sendRequest($request);
        self::assertSame('done', (string) $response1->getBody());

        $response2 = $client->sendRequest($request);
        self::assertSame('DONE', (string) $response2->getBody());

        self::expectExceptionObject(new LogicException('Queue is empty'));
        $client->sendRequest($request);
    }

    public function testFileShttp(): void
    {
        $client = new MockClient(__DIR__ . '/fixtures/RES.200.1.shttp');

        $request = new Request('GET', 'https://example.com/api/endpoint');

        $response1 = $client->sendRequest($request);
        self::assertSame('done', (string) $response1->getBody());
    }
}
