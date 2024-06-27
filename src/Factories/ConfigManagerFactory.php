<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Factories;

use Generator;
use StrictPhp\HttpClients\Clients\CacheResponse\Config as CacheResponseConfig;
use StrictPhp\HttpClients\Clients\CustomizeRequest\Config as CustomizeRequestConfig;
use StrictPhp\HttpClients\Clients\Retry\Config as RetryConfig;
use StrictPhp\HttpClients\Clients\Sleep\Config as SleepConfig;
use StrictPhp\HttpClients\Clients\Store\Config as StoreConfig;
use StrictPhp\HttpClients\Contracts\ConfigContract;
use StrictPhp\HttpClients\Managers\ConfigManager;

/**
 * @phpstan-type config array<class-string<ConfigContract>, array<string, mixed>|null>|array<ConfigContract>
 */
final class ConfigManagerFactory
{
    /**
     * @var array<string, class-string<ConfigContract>>
     */
    private readonly array $configAliases;

    /**
     * @param array<string, class-string<ConfigContract>> $configAliases
     */
    public function __construct(
        private readonly string $keyDefault = 'default',
        array $configAliases = [],
    ) {
        $this->configAliases = $configAliases + [
            'cacheResponse' => CacheResponseConfig::class,
            'customizeRequest' => CustomizeRequestConfig::class,
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
            foreach ($this->buildConfigManager($config[$this->keyDefault]) as $configObject) {
                $configManager->addDefault($configObject);
            }
        }
        unset($config[$this->keyDefault]);

        foreach ($config as $host => $values) {
            foreach ($this->buildConfigManager($values) as $configObject) {
                $configManager->add($host, $configObject);
            }
        }

        return $configManager;
    }

    /**
     * @param config $config
     *
     * @return Generator<ConfigContract>
     */
    private function buildConfigManager(array $config): Generator
    {
        foreach ($config as $configClass => $parameters) {
            if ($parameters === null) {
                continue;
            } elseif ($parameters instanceof ConfigContract) {
                $objectConfig = $parameters;
            } else {
                /** @var class-string<ConfigContract> $class */
                $class = $this->configAliases[$configClass] ?? $configClass;
                $objectConfig = new $class(...$parameters);
            }

            yield $objectConfig;
        }
    }
}
