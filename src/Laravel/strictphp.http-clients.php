<?php declare(strict_types=1);

use StrictPhp\HttpClients\Laravel\HttpClientsServiceProvider;

return [
    HttpClientsServiceProvider::KeyConfig => [],
    HttpClientsServiceProvider::KeyFactories => [
        // list of ClientFactoryContract
    ],
    HttpClientsServiceProvider::KeyStorage => 'http/',
];
