<?php declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withRootFiles()
    ->withPaths([
        __DIR__ . '/src',
        // __DIR__ . '/tests',
    ])
    // This is required to include the StrictPHP Conventions
    ->withSets([StrictPhp\Conventions\ExtensionFiles::Ecs]);
