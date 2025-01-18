<?php declare(strict_types=1);

use StrictPhp\HttpClients\Clients\CacheResponse\CacheResponseClientFactory;
use StrictPhp\HttpClients\Clients\CustomizeRequest\CustomizeRequestClientFactory;
use StrictPhp\HttpClients\Clients\CustomResponse\CustomResponseClientFactory;
use StrictPhp\HttpClients\Clients\CustomResponse\CustomResponseConfig;
use StrictPhp\HttpClients\Clients\Retry\RetryClientFactory;
use StrictPhp\HttpClients\Clients\Sleep\SleepClientFactory;
use StrictPhp\HttpClients\Clients\Store\StoreClientFactory;
use StrictPhp\HttpClients\Laravel\HttpClientsServiceProvider;

return [
    HttpClientsServiceProvider::KeyConfig => [
        'example.com' => [new CustomResponseConfig(true, 'done')],
    ],
    HttpClientsServiceProvider::KeyFactories => [
        CacheResponseClientFactory::class,
        CustomizeRequestClientFactory::class,
        // EventClientFactory::class,
        RetryClientFactory::class,
        SleepClientFactory::class,
        StoreClientFactory::class,
        CustomResponseClientFactory::class,
    ],
    HttpClientsServiceProvider::KeyStorage => 'http/',
];
