# HTTP Clients

## Installation

Let's use composer
```sh
composer require strictphp/http-clients
```

## Clients

### CacheResponseClient [file](src/Clients/CacheResponseClient.php)

> dependent on PSR-6 (simple-cache)

Every request is cached and for next request is used response from cache.
- the development is faster
- you can enable on production option `saveOnly`, download cache and try on localhost
- you can remove headers from prepare cache key [CacheKeyMakerAction.php](src/Actions/CacheKeyMakerAction.php), let's implement own Contract

### CustomResponseClient [file](src/Clients/CustomResponseClient.php)

You can define custom response file. 
The #1 parameter in constructor can be:
 - a path on serialized [SerializableResponse](src/Responses/SerializableResponse.php) which created by [CacheResponseClient](src/Clients/CacheResponse/Client.php)
 - a path on file with plain text

### EventClient [file](src/Clients/EventClient.php)

> dependent on PSR-14 (event-dispatcher)

You can attach events before, failed or request success. It is useful for logging.
- save http file for PhpStrom [SaveForPhpstormRequest.php](src/Requests/SaveForPhpstormRequest.php)
- save response [SaveResponse.php](src/Responses/SaveResponse.php)

### FailedClient [file](src/Clients/FailedClient.php)

This client every time failed and throw ClientExceptionInterface.

> todo

### SleepClient [file](src/Clients/SleepClient.php)

If you need wait interval between requests. Some external API require it.

## ClientsFactory

The factory help you to build client. You define list of ClientFactoryContract and this main factory prepare ClientInterface for your project.

```php
<?php

use Psr\Container\ContainerInterface;use Psr\Http\Client\ClientInterface;use StrictPhp\HttpClients\Clients\CacheResponse\CacheResponseClientFactory;use StrictPhp\HttpClients\Clients\Event\EventClientFactory;use StrictPhp\HttpClients\Clients\Sleep\SleepClientFactory;use Strictphp\HttpClients\Factories\ClientsFactory;use Strictphp\HttpClients\Iterators\FactoryToServiceIterator;

$clients = [
    EventClientFactory::class,
    SleepClientFactory::class,
    CacheResponseClientFactory::class,
];

// $client is main clint like GuzzleHttp\Client
/** @var ClientInterface $client */
/** @var ContainerInterface $container */

$clientFactory = new ClientsFactory($client)
$client = $clientFactory->create(new FactoryToServiceIterator($container, $clients));
// or
$toService = new FactoryToServiceIterator($container, $clients)
$client = (new ClientsFactory($client, $toService))->create();
```
