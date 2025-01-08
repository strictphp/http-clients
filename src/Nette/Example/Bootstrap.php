<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Nette\Example;

use Nette\Bootstrap\Configurator;
use Nette\DI\Container;

class Bootstrap
{
    private readonly Configurator $configurator;
    private readonly string $rootDir;

    public function __construct()
    {
        $this->rootDir = dirname(__DIR__);
        $this->configurator = new Configurator();
        $this->configurator->setTempDirectory($this->rootDir . '/../../tests/temp');
    }

    public function bootWebApplication(): Container
    {
        $this->setupContainer();
        return $this->configurator->createContainer();
    }

    private function setupContainer(): void
    {
        $configDir = $this->rootDir . '/Example';
        $this->configurator->addConfig($configDir . '/http.neon');
    }
}
