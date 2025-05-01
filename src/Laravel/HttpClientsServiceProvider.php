<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Laravel;

use GuzzleHttp\Client;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface;
use Psr\SimpleCache\CacheInterface;
use StrictPhp\HttpClients\Actions\FindExtensionFromHeadersAction;
use StrictPhp\HttpClients\Actions\StreamAction;
use StrictPhp\HttpClients\Clients\CacheResponse\CacheResponseClientFactory;
use StrictPhp\HttpClients\Clients\CustomizeRequest\CustomizeRequestClientFactory;
use StrictPhp\HttpClients\Clients\CustomResponse\CustomResponseClientFactory;
use StrictPhp\HttpClients\Clients\Event\Actions\MakePathAction;
use StrictPhp\HttpClients\Clients\Event\EventClientFactory;
use StrictPhp\HttpClients\Clients\Retry\RetryClientFactory;
use StrictPhp\HttpClients\Clients\Sleep\SleepClientFactory;
use StrictPhp\HttpClients\Clients\Store\StoreClientFactory;
use StrictPhp\HttpClients\Contracts\ClientsFactoryContract;
use StrictPhp\HttpClients\Contracts\FindExtensionFromHeadersActionContract;
use StrictPhp\HttpClients\Contracts\MakePathActionContract;
use StrictPhp\HttpClients\Contracts\StreamActionContract;
use StrictPhp\HttpClients\Exceptions\LogicException;
use StrictPhp\HttpClients\Factories\ClientsFactory;
use StrictPhp\HttpClients\Factories\ConfigManagerFactory;
use StrictPhp\HttpClients\Filesystem\Contracts\FileFactoryContract;
use StrictPhp\HttpClients\Filesystem\Factories\FileFactory;
use StrictPhp\HttpClients\Iterators\FactoryToServiceIterator;
use StrictPhp\HttpClients\Iterators\ReverseIterator;
use StrictPhp\HttpClients\Managers\ConfigManager;
use StrictPhp\HttpClients\Responses\SaveResponse;
use StrictPhp\HttpClients\Services\CachePsr16Service;
use StrictPhp\HttpClients\Services\FilesystemService;
use StrictPhp\HttpClients\Services\SerializableResponseService;
use StrictPhp\HttpClients\Transformers\CacheKeyToFileInfoTransformer;
use Symfony\Component\HttpClient\Psr18Client;

final class HttpClientsServiceProvider extends ServiceProvider
{
    public const ServiceCache = self::Filename . '.Cache';
    public const ServiceMainClient = self::Filename . '.Client';
    public const ServiceFilesystem = self::Filename . '.Filesystem';
    public const KeyStorage = 'storage';
    public const KeyFactories = 'factories';
    public const KeyConfig = 'config';
    private const KeyMain = 'strictphp';
    private const KeyPackage = 'http-clients';
    private const Filename = self::KeyMain . '.' . self::KeyPackage;

    public function register(): void
    {
        parent::register();

        $this->app->singletonIf(ConfigManagerFactory::class, ConfigManagerFactory::class);
        $this->app->singletonIf(FileFactoryContract::class, FileFactory::class);
        $this->app->singletonIf(FindExtensionFromHeadersActionContract::class, FindExtensionFromHeadersAction::class);
        $this->app->singletonIf(MakePathActionContract::class, MakePathAction::class);
        $this->app->singletonIf(SaveResponse::class, SaveResponse::class);
        $this->app->singletonIf(StreamActionContract::class, StreamAction::class);
        // factories
        $this->app->singletonIf(
            CacheResponseClientFactory::class,
            static function (Application $application): CacheResponseClientFactory {
                $cache = $application->make(self::ServiceCache);
                assert($cache instanceof CacheInterface);
                $serializableResponse = $application->make(SerializableResponseService::class);
                $configManager = $application->make(ConfigManager::class);

                return new CacheResponseClientFactory($cache, $serializableResponse, $configManager);
            },
        );
        $this->app->singletonIf(CustomizeRequestClientFactory::class);
        $this->app->singletonIf(CustomResponseClientFactory::class);
        $this->app->singletonIf(EventClientFactory::class);
        $this->app->singletonIf(RetryClientFactory::class);
        $this->app->singletonIf(SleepClientFactory::class);
        $this->app->singletonIf(StoreClientFactory::class);

        $this->app->singletonIf(ClientsFactoryContract::class, static function (
            Application $application,
        ): ClientsFactory {
            $client = $application->make(self::ServiceMainClient);
            assert($client instanceof ClientInterface);
            $reverseFactories = new ReverseIterator(self::myConfig($application, self::KeyFactories, []));

            return new ClientsFactory($client, new FactoryToServiceIterator($application, $reverseFactories));
        });

        $this->app->singletonIf(self::ServiceMainClient, static function (Application $application): ClientInterface {
            if (class_exists(Client::class)) {
                return new Client();
            } elseif (class_exists(Psr18Client::class)) {
                return new Psr18Client();
            }
            throw new LogicException(
                sprintf('Register http client like service name %s.', self::ServiceMainClient),
            );
        });

        $this->app->singletonIf(
            ClientInterface::class,
            static function (Application $application, array $arguments): ClientInterface {
                /** @var array{client?: ClientInterface} $arguments */
                $clientFactory = $application->make(ClientsFactoryContract::class);
                assert($clientFactory instanceof ClientsFactory);

                return $clientFactory->create($arguments['client'] ?? null);
            },
        );

        $this->app->singletonIf(ConfigManager::class, static function (Application $application): ConfigManager {
            $configManagerFactory = $application->make(ConfigManagerFactory::class);

            return $configManagerFactory->create(self::myConfig($application, self::KeyConfig, []));
        });

        $this->app->singletonIf(
            self::ServiceFilesystem,
            static fn (Application $application): Filesystem => new FilesystemService(
                $application->storagePath(self::myConfig($application, self::KeyStorage, 'http/')),
            ),
        );

        $this->app->singletonIf(FileFactory::class, static function (Application $application): FileFactoryContract {
            $filesystem = $application->make(self::ServiceFilesystem);
            assert($filesystem instanceof Filesystem);

            return new FileFactory($filesystem);
        });

        $this->app->singletonIf(
            self::ServiceCache,
            static function (Application $application): CacheInterface {
                $fileFactory = $application->make(FileFactoryContract::class);

                $cacheKeyToFileInfoTransformer = $application->make(CacheKeyToFileInfoTransformer::class);

                return new CachePsr16Service($fileFactory, $cacheKeyToFileInfoTransformer);
            },
        );

        $this->app->singletonIf(
            CacheKeyToFileInfoTransformer::class,
            static fn (): CacheKeyToFileInfoTransformer => new CacheKeyToFileInfoTransformer('cached'),
        );
    }

    /**
     * @template T
     * @param T $default
     *
     * @return T
     */
    private static function myConfig(Application $application, string $key, mixed $default): mixed
    {
        return $application['config'][self::KeyMain][self::KeyPackage][$key] ?? $default; // @phpstan-ignore-line
    }
}
