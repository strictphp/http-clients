<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Services;

use StrictPhp\HttpClients\Contracts\ConfigContract;
use StrictPhp\HttpClients\Exceptions\InvalidStateException;

final class ConfigService
{
    /**
     * @var array<class-string<ConfigContract>, array<string, ConfigContract>>
     */
    private array $configs = [];

    /**
     * @var array<class-string<ConfigContract>, ConfigContract>
     */
    private array $defaults = [];


    public function add(string $host, ConfigContract $config): void
    {
        $config->initFromDefaultConfig($this->getDefault($config::class));
        $this->configs[$config::class][$host] = $config;
    }

    /**
     * @param class-string<ConfigContract> $class
     */
    private function getDefault(string $class): ConfigContract
    {
        if (array_key_exists($class, $this->defaults) === false) {
            $this->addDefault(new $class());
        }

        return $this->defaults[$class];
    }

    public function addDefault(ConfigContract $config): void
    {
        if (array_key_exists($config::class, $this->defaults)) {
            throw new InvalidStateException(sprintf('Default config for "%s" already exists.', $config::class));
        }
        $this->defaults[$config::class] = $config;
    }

    /**
     * @template T of ConfigContract
     * @param class-string<T> $class
     *
     * @return T
     */
    public function get(string $class, string $host): ConfigContract
    {
        $config = $this->configs[$class][$host] ?? $this->getDefault($class);
        assert(is_a($config, $class));

        return $config;
    }
}
