# Http for Laravel

Do you have a preferred HTTP client implementing PSR-18 (e.g., guzzlehttp/guzzle, symfony/http-client, etc.)? This tool brings clarity to outgoing communication, allowing you to log requests, retry them, introduce delays between requests to the same host, cache requests for development, and debug using generated files for PhpStorm, which you can execute via the IDE. Configurations can be applied globally or individually, depending on the server you're communicating with.

## How It Works

Essentially, it's a package of classes implementing PSR-18, with each implementation serving as a middleware layer. A [list of clients](https://github.com/strictphp/http-clients?tab=readme-ov-file#features) is also available in the original guide.

```sh
composer require strictphp/http-clients
```

If you don't have an HTTP client, simply install one:

```sh
composer require guzzlehttp/guzzle guzzlehttp/psr7
```

### Registering the ServiceProvider

First, register the service provider:

```php
'providers' => [
    StrictPhp\HttpClients\Laravel\HttpClientsServiceProvider::class,
]
```

If we have `guzzlehttp/guzzle` or `symfony/http-client` in the project, the ServiceProvider will find and automatically connect it.
If we have another PSR-18 implementation or need to change the default client parameters, adjustments are necessary.
In the example, we chose Guzzle.

```php
use GuzzleHttp\Client;
use StrictPhp\HttpClients\Laravel\HttpClientsServiceProvider;

final class MyServiceProvider extends \Illuminate\Support\ServiceProvider {

    public function register() {
        parent::register();
        $this->app->singleton(HttpClientsServiceProvider::ServiceMainClient, static fn (): ClientInterface => new Client(['timeout' => 5.0]));
    }

}
```

At this point, DI for the `Psr\Http\Client\ClientInterface` interface works in the application, and the class we get is `StrictPhp\HttpClients\Clients\MainHttp\MainHttpClient`. The `MainHttpClient` class is the entry point for all middlewares, so you can set a breakpoint there and start stepping through.

Currently, we're not utilizing any features provided by the library. So, we'll enable the middlewares we need by registering factory classes. The order matters; it's up to us which ones we enable. We'll use a prepared configuration array. The configuration file must be named [strictphp.http-clients.php](strictphp.http-clients.php).

```php
use StrictPhp\HttpClients\Clients;

return [
    // 'config' => [],
    'factories' => [
        Clients\CacheResponse\CacheResponseClientFactory::class,
        Clients\CacheResponse\RetryClientFactory::class,
        Clients\CacheResponse\SleepClientFactory::class,
        Clients\Store\StoreClientFactory::class,
    ],
    // 'storage' => 'http/',
];
```

Now, let's see how to configure requests for hosts. Each middleware has a corresponding configuration class; for example, `CacheResponseClient` has `CacheResponseConfig`.

```php
use StrictPhp\HttpClients\Clients;

return [
    'config' => [
        'default' => [ // shared configuration for all domains
            Clients\CacheResponse\CacheResponseConfig(604000, saveOnly: true)
        ],
        'example.com' => [
            Clients\CacheResponse\CacheResponseConfig(enabled: false), // disables cache for example.com
            Clients\Sleep\SleepConfig(enabled: false), // disables sleep for example.com
        ],
    ],
    //'factories' => [
    //    // list of ClientFactoryContract
    //],
    // 'storage' => 'http/',
];
```

## Custom Middleware

Instructions on how to write your own middleware are described in the [original guide](https://github.com/strictphp/http-clients?tab=readme-ov-file#write-your-own-client).
