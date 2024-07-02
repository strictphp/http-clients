<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Managers;

use StrictPhp\HttpClients\Contracts\ConfigInterface;
use StrictPhp\HttpClients\Exceptions\InvalidStateException;
use StrictPhp\HttpClients\Helpers\Host;

final class ConfigManager
{
    /**
     * @param array<string, array<class-string<ConfigInterface>, ConfigInterface>> $configs
     * @param array<class-string<ConfigInterface>, ConfigInterface> $defaults
     */
    public function __construct(
        private array $configs = [],
        private array $defaults = [],
    ) {
    }

    /**
     * @param ConfigInterface|iterable<ConfigInterface> $configs
     */
    public function add(string $host, ConfigInterface|iterable $configs): void
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
     * @param ConfigInterface|iterable<ConfigInterface> $configs
     */
    public function addDefault(ConfigInterface|iterable $configs): void
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
     * @template T of ConfigInterface
     * @param class-string<T> $class
     *
     * @return T
     */
    public function get(string $class, string $host): ConfigInterface
    {
        $config = $this->configs[$host][$class]
            ?? $this->configs[Host::allSubdomains($host)][$class]
            ?? $this->getDefault($class);
        assert(is_a($config, $class));

        return $config;
    }

    /**
     * @param class-string<ConfigInterface> $class
     */
    private function getDefault(string $class): ConfigInterface
    {
        if ($this->defaultExists($class) === false) {
            $this->forceDefault(new $class());
        }

        return $this->defaults[$class];
    }

    private function forceDefault(ConfigInterface $config): void
    {
        $this->defaults[$config::class] = $config;
    }

    /**
     * @param class-string<ConfigInterface> $class
     */
    private function defaultExists(string $class): bool
    {
        return array_key_exists($class, $this->defaults);
    }
}
