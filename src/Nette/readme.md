# HTTP for Nette

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

### NEON Configuration

First, register the extension:

```neon
extensions:
    psrHttp: StrictPhp\HttpClients\Nette\DI\HttpExtension(%tempDir%/http, %logDir%/http)
```

To ensure everything functions correctly, connect the main HTTP client; in this example, we've chosen Guzzle:

```neon
services:
    psrHttp.main.client: GuzzleHttp\Client() # autowire automatically set to false
```

At this point, DI for the `Psr\Http\Client\ClientInterface` interface works in the application, with `StrictPhp\HttpClients\Clients\MainHttp\MainHttpClient` as the class. The `MainHttpClient` class is the entry point for all middlewares, so you can set a breakpoint there and start stepping through.

Currently, we're not utilizing any features provided by the library. Let's enable the middlewares we need. The order matters; it's up to us which ones we activate:

```neon
services:
    psrHttp.middlewares:
        arguments:
            -
                - @psrHttp.middleware.cacheResponse
                - @psrHttp.middleware.retry
                - @psrHttp.middleware.sleep
                - @psrHttp.middleware.store
                - @psrHttp.middleware.customizeRequest
                # debug
                # - @psrHttp.middleware.customResponse
                # only if you have another library implementing PSR-14 (EventDispatcherInterface)
                # - @psrHttp.middleware.event
```

Now, let's configure requests for hosts. Each middleware has a corresponding configuration class; for example, `CacheResponseClient` has `CacheResponseConfig`:

```neon
services:
    psrHttp.config.manager:
        setup:
            # for all hosts
            - addDefault(StrictPhp\HttpClients\Clients\CacheResponse\CacheResponseConfig(604000, saveOnly: true))
            - addDefault(StrictPhp\HttpClients\Clients\Store\StoreConfig(serialized: false, onSuccess: false))
            # host-specific configuration
            - add('www.cnb.cz', [
                StrictPhp\HttpClients\Clients\CacheResponse\CacheResponseConfig(60, saveOnly: false),
                StrictPhp\HttpClients\Clients\Store\StoreConfig(serialized: false, onSuccess: true), # saves every request to cache
            ])
```

## Custom Middleware

Instructions for writing custom middleware are described in the [original guide](https://github.com/strictphp/http-clients?tab=readme-ov-file#write-your-own-client). For Nette, implementing `StrictPhp\HttpClients\Contracts\ClientFactoryContract` isn't necessary; Nette can generate this class automatically:

```neon
services:
    my.middleware:
        implement: StrictPhp\HttpClients\Contracts\ClientFactoryContract
        type: MyMiddleWareClient
        autowired: false

    psrHttp.middlewares:
        arguments:
            -
                - @my.middleware
```

## Description of CacheResponseClient

Files in the log are organized by `<date>/<host>/<hour>/<name of file>`. In the temp directory, there's only cache; when `saveOnly: true` is set, it only writes to the cache, which can be transferred between machines.

![image](../../.github/request-filesystem.png)

An example of a generated file for PhpStorm (*.REQ.http):

```http
### Duration: 0.22270321846008
GET https://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/denni_kurz.txt
Host: www.cnb.cz
```

## Conclusion

A fully functional example is available in the repository under the [src/Nette/Example](Example) directory. Simply run `php src/Nette/Example/index.php`.
