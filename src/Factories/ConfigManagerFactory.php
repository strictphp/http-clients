<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Factories;

use Generator;
use StrictPhp\HttpClients\Clients\CacheResponse\CacheResponseConfig;
use StrictPhp\HttpClients\Clients\CustomizeRequest\CustomizeRequestConfig;
use StrictPhp\HttpClients\Clients\Event\EventConfig;
use StrictPhp\HttpClients\Clients\Retry\RetryConfig;
use StrictPhp\HttpClients\Clients\Sleep\SleepConfig;
use StrictPhp\HttpClients\Clients\Store\StoreConfig;
use StrictPhp\HttpClients\Contracts\ConfigInterface;
use StrictPhp\HttpClients\Managers\ConfigManager;

/**
 * @phpstan-type config array<class-string<ConfigInterface>, array<string, mixed>|null>|array<ConfigInterface>
 */
final class ConfigManagerFactory
{
    /**
     * @var array<string, class-string<ConfigInterface>>
     */
    private readonly array $configAliases;

    /**
     * @param array<string, class-string<ConfigInterface>> $configAliases
     */
    public function __construct(
        private readonly string $keyDefault = 'default',
        array $configAliases = [],
    ) {
        $this->configAliases = $configAliases + [
            'cacheResponse' => CacheResponseConfig::class,
            'customizeRequest' => CustomizeRequestConfig::class,
            'event' => EventConfig::class,
            'retry' => RetryConfig::class,
            'sleep' => SleepConfig::class,
            'store' => StoreConfig::class,
        ];
    }

    /**
     * config array represent
     * [
     *  host => [Config::class / alias => [constructor parameters]]
     * ]
     *
     * [
     *  'strictphp.com' => [
     *     StrictPhp\HttpClients\Clients\CacheResponse\Config::class => [
     *      'ttl' => null,
     *      'saveOnly' => true,
     *     ],
     *     // or use alias
     *     'cacheResponse' => [
     *      'ttl' => null,
     *      'saveOnly' => true,
     *     ],
     *     new StrictPhp\HttpClients\Clients\Sleep\Config(300, 1000),
     *   ]
     * ]
     * @param array<string, config> $config
     */
    public function create(array $config): ConfigManager
    {
        $configManager = new ConfigManager();

        if (isset($config[$this->keyDefault])) {
            $configManager->addDefault($this->buildConfigManager($config[$this->keyDefault]));
        }
        unset($config[$this->keyDefault]);

        foreach ($config as $host => $values) {
            $configManager->add($host, $this->buildConfigManager($values));
        }

        return $configManager;
    }

    /**
     * @param config $config
     *
     * @return Generator<ConfigInterface>
     */
    private function buildConfigManager(array $config): Generator
    {
        foreach ($config as $configClass => $parameters) {
            if ($parameters === null) {
                continue;
            } elseif ($parameters instanceof ConfigInterface) {
                $objectConfig = $parameters;
            } else {
                /** @var class-string<ConfigInterface> $class */
                $class = $this->configAliases[$configClass] ?? $configClass;
                $objectConfig = new $class(...$parameters);
            }

            yield $objectConfig;
        }
    }
}
