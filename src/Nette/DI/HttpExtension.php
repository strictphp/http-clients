<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Nette\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Actions\FindExtensionFromHeadersAction;
use StrictPhp\HttpClients\Actions\StreamAction;
use StrictPhp\HttpClients\Clients\CacheResponse\Actions\CacheKeyMakerAction;
use StrictPhp\HttpClients\Clients\CacheResponse\CacheResponseClient;
use StrictPhp\HttpClients\Clients\CustomizeRequest\CustomizeRequestClient;
use StrictPhp\HttpClients\Clients\CustomResponse\CustomResponseClientFactory;
use StrictPhp\HttpClients\Clients\Event\Actions\MakePathAction;
use StrictPhp\HttpClients\Clients\Event\EventClient;
use StrictPhp\HttpClients\Clients\Retry\RetryClient;
use StrictPhp\HttpClients\Clients\Sleep\SleepClient;
use StrictPhp\HttpClients\Clients\Store\StoreClient;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;
use StrictPhp\HttpClients\Factories\ClientsFactory;
use StrictPhp\HttpClients\Filesystem\Factories\FileFactory;
use StrictPhp\HttpClients\Iterators\ReverseIterator;
use StrictPhp\HttpClients\Managers\ConfigManager;
use StrictPhp\HttpClients\Requests\SaveForPhpstormRequest;
use StrictPhp\HttpClients\Responses\SaveResponse;
use StrictPhp\HttpClients\Services\CachePsr16Service;
use StrictPhp\HttpClients\Services\CacheRequestService;
use StrictPhp\HttpClients\Services\FilesystemService;

class HttpExtension extends CompilerExtension
{
    public function __construct(
        private readonly string $tempDir,
        private readonly string $logDir,
    ) {
    }

    public function loadConfiguration(): void
    {
        $this->buildMainClient();
        $this->buildCacheKeyMaker();
        $this->buildInternalServices();
        $this->buildMiddlewares();
        $this->buildClients();
        $this->buildConfigManager();
        $this->buildCache();
        $this->buildClientFactory();
        $this->buildHttpClient();
    }

    public function beforeCompile(): void
    {
        $eventDispatcher = $this->getContainerBuilder()
            ->getByType(EventDispatcherInterface::class);

        if ($eventDispatcher !== null) {
            $this->buildClient(EventClient::class, 'event');
        }
    }

    private function buildMainClient(): void
    {
        $this->getContainerBuilder()
            ->addDefinition($this->prefix('main.client'))
            ->setType(ClientInterface::class)
            ->setAutowired(false);
    }

    private function buildMiddlewares(): void
    {
        $this->getContainerBuilder()
            ->addDefinition($this->prefix('middlewares'))
            ->setCreator(ReverseIterator::class, [[]])
            ->setAutowired(false);
    }

    private function buildClients(): void
    {
        foreach (
            [
                'cacheResponse' => CacheResponseClient::class,
                'store' => StoreClient::class,
                'sleep' => SleepClient::class,
                'retry' => RetryClient::class,
                'customizeRequest' => CustomizeRequestClient::class,
            ] as $alias => $class
        ) {
            $this->buildClient($class, $alias);
        }

        $this->buildCustomResponse();
    }

    private function buildCustomResponse(): void
    {
        $this->getContainerBuilder()
            ->addDefinition($this->prefix('middleware.customResponse'))
            ->setCreator(CustomResponseClientFactory::class, ['success'])
            ->setAutowired(false);
    }

    /**
     * @param class-string<ClientInterface> $class
     */
    private function buildClient(string $class, string $alias): void
    {
        $this->getContainerBuilder()
            ->addFactoryDefinition($this->prefix('middleware.' . $alias))
            ->setImplement(ClientFactoryContract::class)
            ->setResultDefinition((new ServiceDefinition())->setCreator($class))
            ->setAutowired(false);
    }

    private function buildConfigManager(): void
    {
        $this->getContainerBuilder()
            ->addDefinition($this->prefix('config.manager'))
            ->setCreator(ConfigManager::class);
    }

    private function buildCache(): void
    {
        $this->getContainerBuilder()
            ->addDefinition($this->prefix('cache'))
            ->setCreator(CachePsr16Service::class, [$this->prefix('@file.factory.temp')])
            ->setAutowired(false);
    }

    private function buildClientFactory(): void
    {
        $this->getContainerBuilder()
            ->addDefinition($this->prefix('client.factory'))
            ->setCreator(ClientsFactory::class, [$this->prefix('@main.client'), $this->prefix('@middlewares')])
            ->setAutowired(false);
    }

    private function buildHttpClient(): void
    {
        $this->getContainerBuilder()
            ->addDefinition($this->prefix('client'))
            ->setCreator([$this->prefix('@client.factory'), 'create']);
    }

    private function buildCacheKeyMaker(): void
    {
        $this->getContainerBuilder()
            ->addDefinition($this->prefix('cache.key.maker'))
            ->setCreator(CacheKeyMakerAction::class);
    }

    private function buildInternalServices(): void
    {
        $this->getContainerBuilder()
            ->addDefinition($this->prefix('cache.request.service'))
            ->setCreator(CacheRequestService::class, [$this->prefix('@cache')]);

        self::makeDirectory($this->tempDir);
        $this->getContainerBuilder()
            ->addDefinition($this->prefix('filesystem.temp'))
            ->setCreator(FilesystemService::class, [$this->tempDir])
            ->setAutowired(false);

        self::makeDirectory($this->logDir);
        $this->getContainerBuilder()
            ->addDefinition($this->prefix('filesystem.log'))
            ->setCreator(FilesystemService::class, [$this->logDir])
            ->setAutowired(false);

        $this->getContainerBuilder()
            ->addDefinition($this->prefix('file.factory.log'))
            ->setCreator(FileFactory::class, [$this->prefix('@filesystem.log')])
            ->setAutowired(false);

        $this->getContainerBuilder()
            ->addDefinition($this->prefix('file.factory.temp'))
            ->setCreator(FileFactory::class, [$this->prefix('@filesystem.temp')])
            ->setAutowired(false);

        $this->getContainerBuilder()
            ->addDefinition($this->prefix('make.path'))
            ->setCreator(MakePathAction::class)
            ->setAutowired(false);

        $this->getContainerBuilder()
            ->addDefinition($this->prefix('extension.header'))
            ->setCreator(FindExtensionFromHeadersAction::class)
            ->setAutowired(false);

        $this->getContainerBuilder()
            ->addDefinition($this->prefix('stream'))
            ->setCreator(StreamAction::class)
            ->setAutowired(false);

        $this->getContainerBuilder()
            ->addDefinition($this->prefix('save.response'))
            ->setCreator(SaveResponse::class, [
                $this->prefix('@file.factory.log'),
                $this->prefix('@make.path'),
                $this->prefix('@extension.header'),
                $this->prefix('@stream'),
            ])
            ->setAutowired(false);

        $this->getContainerBuilder()
            ->addDefinition($this->prefix('save.for.phpstorm'))
            ->setCreator(SaveForPhpstormRequest::class, [
                $this->prefix('@file.factory.log'),
                $this->prefix('@make.path'),
                $this->prefix('@save.response'),
                $this->prefix('@stream'),
            ]);
    }

    private static function makeDirectory(string $path): void
    {
        @mkdir($path, 0777, true);
    }
}
