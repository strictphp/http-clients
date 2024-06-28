<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Managers;

use StrictPhp\HttpClients\Contracts\ConfigContract;
use StrictPhp\HttpClients\Exceptions\InvalidStateException;
use StrictPhp\HttpClients\Helpers\Host;

final class ConfigManager
{
    /**
     * @param array<string, array<class-string<ConfigContract>, ConfigContract>> $configs
     * @param array<class-string<ConfigContract>, ConfigContract> $defaults
     */
    public function __construct(
        private array $configs = [],
        private array $defaults = [],
    ) {
    }

    /**
     * @param ConfigContract|iterable<ConfigContract> $configs
     */
    public function add(string $host, ConfigContract|iterable $configs): void
    {
        if (is_iterable($configs)) {
            foreach ($configs as $config) {
                $this->add($host, $config);
            }
        } else {
            $configs->initFromDefaultConfig($this->getDefault($configs::class));
            $this->configs[$host][$configs::class] = $configs;
        }
    }

    /**
     * @param ConfigContract|iterable<ConfigContract> $configs
     */
    public function addDefault(ConfigContract|iterable $configs): void
    {
        if (is_iterable($configs)) {
            foreach ($configs as $config) {
                $this->addDefault($config);
            }
        } else {
            if ($this->defaultExists($configs::class)) {
                throw new InvalidStateException(sprintf('Default config for "%s" already exists.', $configs::class));
            }
            $this->forceDefault($configs);
        }
    }

    /**
     * @template T of ConfigContract
     * @param class-string<T> $class
     *
     * @return T
     */
    public function get(string $class, string $host): ConfigContract
    {
        $config = $this->configs[$host][$class]
            ?? $this->configs[Host::allSubdomains($host)][$class]
            ?? $this->getDefault($class);
        assert(is_a($config, $class));

        return $config;
    }

    /**
     * @param class-string<ConfigContract> $class
     */
    private function getDefault(string $class): ConfigContract
    {
        if ($this->defaultExists($class) === false) {
            $this->forceDefault(new $class());
        }

        return $this->defaults[$class];
    }

    private function forceDefault(ConfigContract $config): void
    {
        $this->defaults[$config::class] = $config;
    }

    /**
     * @param class-string<ConfigContract> $class
     */
    private function defaultExists(string $class): bool
    {
        return array_key_exists($class, $this->defaults);
    }
}
