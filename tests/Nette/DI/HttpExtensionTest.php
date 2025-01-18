<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Tests\Nette\DI;

use GuzzleHttp\Psr7\HttpFactory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\DI\Definitions\Statement;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Clients\CacheResponse\CacheResponseConfig;
use StrictPhp\HttpClients\Clients\MainHttp\MainHttpClient;
use StrictPhp\HttpClients\Contracts\CacheKeyMakerActionContract;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;
use StrictPhp\HttpClients\Contracts\ClientsFactoryContract;
use StrictPhp\HttpClients\Contracts\FindExtensionFromHeadersActionContract;
use StrictPhp\HttpClients\Contracts\MakePathActionContract;
use StrictPhp\HttpClients\Contracts\StreamActionContract;
use StrictPhp\HttpClients\Filesystem\Contracts\FileFactoryContract;
use StrictPhp\HttpClients\Iterators\ReverseIterator;
use StrictPhp\HttpClients\Managers\ConfigManager;
use StrictPhp\HttpClients\Nette\DI\HttpExtension;
use StrictPhp\HttpClients\Requests\SaveForPhpstormRequest;
use StrictPhp\HttpClients\Responses\SaveResponse;
use StrictPhp\HttpClients\Services\CacheRequestService;
use Symfony\Component\HttpClient\Psr18Client;

final class HttpExtensionTest extends TestCase
{
    public function testNoConfig(): void
    {
        $container = $this->createContainer();
        $httpClient = $container->getService('psrHttp.client');

        Assert::assertTrue($httpClient instanceof MainHttpClient);
    }

    public function testBuildClient(): void
    {
        $container = $this->createContainer([
            'psrHttp.main.client' => Psr18Client::class,
            EventDispatcherMock::class,
            'psrHttp.config.manager' => [
                'setup' => [
                    new Statement(
                        ['@self', 'addDefault'],
                        [new Statement(CacheResponseConfig::class, [86000, true, true])],
                    ),
                ],
            ],
            'psrHttp.middlewares' => [
                'arguments' => [
                    [
                        '@psrHttp.middleware.cacheResponse',
                        '@psrHttp.middleware.retry',
                        '@psrHttp.middleware.sleep',
                        '@psrHttp.middleware.store',
                        '@psrHttp.middleware.event',
                        '@psrHttp.middleware.customResponse',
                    ],
                ],
            ],
        ]);

        $httpClient = $container->getService('psrHttp.client');
        Assert::assertTrue($httpClient instanceof MainHttpClient);

        Assert::assertTrue($container->getService('psrHttp.middlewares') instanceof ReverseIterator);

        Assert::assertTrue($container->getService('psrHttp.middleware.cacheResponse') instanceof ClientFactoryContract);
        Assert::assertTrue($container->getService('psrHttp.middleware.store') instanceof ClientFactoryContract);
        Assert::assertTrue($container->getService('psrHttp.middleware.sleep') instanceof ClientFactoryContract);
        Assert::assertTrue($container->getService('psrHttp.middleware.retry') instanceof ClientFactoryContract);
        Assert::assertTrue($container->getService('psrHttp.middleware.event') instanceof ClientFactoryContract);
        Assert::assertTrue(
            $container->getService('psrHttp.middleware.customResponse') instanceof ClientFactoryContract,
        );
        Assert::assertTrue(
            $container->getService('psrHttp.middleware.customizeRequest') instanceof ClientFactoryContract,
        );

        Assert::assertTrue($container->getService('psrHttp.config.manager') instanceof ConfigManager);

        Assert::assertTrue($container->getService('psrHttp.cache') instanceof CacheInterface);

        Assert::assertTrue($container->getService('psrHttp.client.factory') instanceof ClientsFactoryContract);

        Assert::assertTrue($container->getService('psrHttp.cache.key.maker') instanceof CacheKeyMakerActionContract);
        Assert::assertTrue($container->getService('psrHttp.cache.request.service') instanceof CacheRequestService);
        Assert::assertTrue($container->getService('psrHttp.filesystem.temp') instanceof Filesystem);
        Assert::assertTrue($container->getService('psrHttp.filesystem.log') instanceof Filesystem);
        Assert::assertTrue($container->getService('psrHttp.file.factory.log') instanceof FileFactoryContract);
        Assert::assertTrue($container->getService('psrHttp.file.factory.temp') instanceof FileFactoryContract);
        Assert::assertTrue($container->getService('psrHttp.make.path') instanceof MakePathActionContract);
        Assert::assertTrue(
            $container->getService('psrHttp.extension.header') instanceof FindExtensionFromHeadersActionContract,
        );
        Assert::assertTrue($container->getService('psrHttp.stream') instanceof StreamActionContract);
        Assert::assertTrue($container->getService('psrHttp.save.response') instanceof SaveResponse);
        Assert::assertTrue($container->getService('psrHttp.save.for.phpstorm') instanceof SaveForPhpstormRequest);
    }

    /**
     * @param array<mixed> $config
     */
    private function createContainer(array $config = []): Container
    {
        $rootTempDir = __DIR__ . '/../../temp';
        $logDir = $rootTempDir . '/log';
        $tempDir = $rootTempDir . '/tmp';

        $loader = new ContainerLoader($rootTempDir, true);
        $class = $loader->load(function (Compiler $compiler) use ($config, $tempDir, $logDir): void {
            $compiler->addExtension('psrHttp', new HttpExtension($tempDir, $logDir));

            $config['psrHttp.factory'] = HttpFactory::class;
            $compiler->addConfig([
                'services' => $config,
            ]);
        }, md5(strval(microtime(true))));

        $container = new $class();
        assert($container instanceof Container);

        return $container;
    }
}
