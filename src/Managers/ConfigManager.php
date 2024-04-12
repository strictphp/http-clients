<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Managers;

use StrictPhp\HttpClients\Contracts\ConfigContract;
use StrictPhp\HttpClients\Exceptions\InvalidStateException;

final class ConfigManager
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

    public function addDefault(ConfigContract $config): void
    {
        if ($this->existsDefault($config::class)) {
            throw new InvalidStateException(sprintf('Default config for "%s" already exists.', $config::class));
        }
        $this->addDefaultForce($config);
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
        assert($config instanceof $class);

        return $config;
    }

    /**
     * @param class-string<ConfigContract> $class
     */
    private function getDefault(string $class): ConfigContract
    {
        if ($this->existsDefault($class) === false) {
            $this->addDefaultForce(new $class());
        }

        return $this->defaults[$class];
    }

    private function addDefaultForce(ConfigContract $config): void
    {
        $this->defaults[$config::class] = $config;
    }

    /**
     * @param class-string<ConfigContract> $class
     */
    private function existsDefault(string $class): bool
    {
        return array_key_exists($class, $this->defaults);
    }
}
