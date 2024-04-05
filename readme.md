# HTTP Clients

The HTTP Clients package provides a collection of HTTP clients that can be used to manage HTTP requests and responses in your PHP application. The package includes a ClientsFactory to simplify the creation of clients by allowing you to define a list of ClientFactoryContract implementations. 

## Features

- Uses PSR container for dependency injection.
- **[CacheResponseClient](#cacheresponseclient-file)**: Utilizes PSR-6 (simple-cache) for caching responses, improving development speed by serving cached responses for subsequent requests.
- **[EventClient](#eventclient-file)**: Dependent on PSR-14 (event-dispatcher) and enables you to attach events before, during, or after a request, which is useful for logging or other actions.
- **[SleepClient](#sleepclient-file)**: Allows you to introduce a wait interval between requests, which may be necessary for interacting with external APIs that require rate limiting.
- Save your requests as PHPStorm `.http` file and corresponding response as a file.

## Installation

You can install the HTTP Clients package via Composer:

```sh
composer require strictphp/http-clients
```

## Usage

The ClientsFactory simplifies the creation of clients by allowing you to define a list of ClientFactoryContract implementations with dependency injection. 

Example:
```php
use StrictPhp\HttpClients\Clients\CacheResponse\CacheResponseClientFactory;
use StrictPhp\HttpClients\Clients\Event\EventClientFactory;
use StrictPhp\HttpClients\Clients\Sleep\SleepClientFactory;
use Strictphp\HttpClients\Factories\ClientsFactory;
use Strictphp\HttpClients\Iterators\FactoryToServiceIterator;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;

// Assuming $client is the main client like GuzzleHttp\Client
/** @var ClientInterface $client */
/** @var ContainerInterface $container */

$clients = [
    EventClientFactory::class,
    SleepClientFactory::class,
    CacheResponseClientFactory::class,
    // Other client factories...
];

$clientFactory = new ClientsFactory($client);
$client = $clientFactory->create(new FactoryToServiceIterator($container, $clients));
// Alternatively:
// $toService = new FactoryToServiceIterator($container, $clients);
// $client = (new ClientsFactory($client, $toService))->create();
```

These examples demonstrate how to efficiently manage HTTP requests and responses in your PHP application using the provided HTTP client classes and the ClientsFactory.

## Clients

### CacheResponseClient ([file](src/Clients/CacheResponse/CacheResponseClient.php))

The CacheResponseClient utilizes PSR-6 (simple-cache) for caching responses, improving development speed by serving cached responses for subsequent requests. Here are some benefits and considerations:

- **Development Efficiency**: Speeds up development by caching responses, reducing the need for repeated API calls during development.
- **Local Testing**: Enable the `saveOnly` option in production to cache responses and download them for testing on localhost, ensuring consistency and performance.
- **Customization**: Customize cache key preparation by implementing your own contract in [CacheKeyMakerAction.php](src/Actions/CacheKeyMakerAction.php).

Example:
```php
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use StrictPhp\HttpClients\Clients\CacheResponse\CacheResponseClientFactory;
use StrictPhp\HttpClients\Clients\Event\EventClientFactory;
use StrictPhp\HttpClients\Clients\Sleep\SleepClientFactory;
use Strictphp\HttpClients\Factories\ClientsFactory;
use Strictphp\HttpClients\Iterators\FactoryToServiceIterator;

// Assuming $client is the main client like GuzzleHttp\Client
/** @var ClientInterface $client */
/** @var ContainerInterface $container */

// the order of classes is important
$clients = [
    CacheResponseClientFactory::class, // used like first
    SleepClientFactory::class,
    EventClientFactory::class,
    // Other client factories...
];

/**
 * This iterator change array<class-string<ClientFactoryContract>> to array<ClientFactoryContract>
 */
$toService = new FactoryToServiceIterator($container, $clients);

$clientFactory = new ClientsFactory($client);
$client = $clientFactory->create($toService);
// Alternatively, you can use second parameter of constructor:
$clientFactory = new ClientsFactory($client, $toService);
$client = $clientFactory->create();
```

### CustomResponseClient ([file](src/Clients/CustomResponse/CustomResponseClient.php))

> Subject to change.

You can define custom response file.

The #1 parameter in constructor can be:

- a path on serialized [SerializableResponse](src/Responses/SerializableResponse.php) which created by [SaveResponse.php](src/Responses/SaveResponse.php), file with extension `shttp` it's mean `serialized http`
- a path on file with plain text, this is used like body only

You need to set up container dependency to add the content you need.

### EventClient ([file](src/Clients/Event/EventClient.php))

> dependent on PSR-14 (event-dispatcher)

You can attach events before, failed or request success. It is useful for logging.

- save http file for PHPStorm [SaveForPhpstormRequest.php](src/Requests/SaveForPhpstormRequest.php)
- save response [SaveResponse.php](src/Responses/SaveResponse.php)

### FailedClient ([file](src/Clients/Failed/FailedClient.php))

> Subject to change.

The FailedClient always fails and throws ClientExceptionInterface. This client could be useful for testing error handling mechanisms in your application.

### SleepClient ([file](src/Clients/Sleep/SleepClient.php))

The SleepClient allows you to introduce a wait interval between requests, which may be necessary for interacting with external APIs that require rate limiting.
