<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Tests\Laravel;

use GuzzleHttp\Psr7\Request;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Clients\CustomResponse\CustomResponseConfig;
use StrictPhp\HttpClients\Clients\MainHttp\MainHttpClient;
use StrictPhp\HttpClients\Laravel\HttpClientsServiceProvider;
use StrictPhp\HttpClients\Managers\ConfigManager;

final class HttpClientsServiceProviderTest extends TestCase
{
    public function testEmpty(): void
    {
        $application = new Application();
        $application->singleton('config', fn () => new Repository([]));

        $httpClientsServiceProvider = new HttpClientsServiceProvider($application);
        $application->register($httpClientsServiceProvider);

        $client = $application->make(ClientInterface::class);
        assert($client instanceof ClientInterface);

        Assert::assertSame(MainHttpClient::class, $client::class);
    }

    public function testFully(): void
    {
        $application = new Application(__DIR__ . '/../temp/Laravel');
        $application->singleton(
            'config',
            fn () => new Repository(
                [
                    'strictphp' => [
                        'http-clients' => require __DIR__ . '/strictphp.http-clients.php',
                    ],
                ],
            ),
        );

        $httpClientsServiceProvider = new HttpClientsServiceProvider($application);
        $application->register($httpClientsServiceProvider);

        $client = $application->make(ClientInterface::class);
        assert($client instanceof ClientInterface);
        Assert::assertSame(MainHttpClient::class, $client::class);

        $configManager = $application->make(ConfigManager::class);
        assert($configManager instanceof ConfigManager);
        $config = $configManager->get(CustomResponseConfig::class, 'example.com');
        Assert::assertSame('done', $config->content);
        Assert::assertTrue($config->enabled);

        $response = $client->sendRequest(new Request('GET', 'https://example.com/lorem-ipsum'));
        Assert::assertSame('done', $response->getBody()->getContents());

        Assert::assertTrue(is_dir(__DIR__ . '/../temp/Laravel/storage/http/cached/example.com'));
        Assert::assertTrue(
            is_dir(__DIR__ . '/../temp/Laravel/storage/http/' . date('Y-m-d') . '/example.com/' . date('H')),
        );
    }
}
