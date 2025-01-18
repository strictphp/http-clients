# Http pro Laravel

Máte oblíbeného http klienta implementujícího PSR-18 (guzzlehttp/guzzle, symfony/http-client, atd...). Nástroj vám vnese světlo do odchozí komunikace, umožní logovat requesty, opakovat requesty, dát časovou mezeru mezi requesty na stejného hosta, kešovat requesty pro vývoj a debugovat v podobě generovaných souborů pro phpstrom, které sputíte přes IDE. Konfigurace lze dělat pro všechny hosty nebo jednotlivě zvlášť, podle potřeb serveru se kterým komunikujeme.

## Jak to funguje

V principu se jedná o balík tříd implementující PSR-18 a každá implementace je vrstva pro middleware. [Seznam klientů](https://github.com/strictphp/http-clients?tab=readme-ov-file#features) je také v originálním návodu.

```sh
composer require strictphp/http-clients
```

Pokud nemáte žádného http klienta, stačí nainstalovat.

```sh
composer require guzzlehttp/guzzle
```

### Registrace ServiceProvider

Použijte následující třídu

```php
'providers' => [
    StrictPhp\HttpClients\Laravel\HttpClientsServiceProvider::class,
]
```

Aby všechno dobře fungovalo, je potřeba připojit hlavního http klienta, v přikladu jsme zvolili guzzle.

```php
use GuzzleHttp\Client;
use StrictPhp\HttpClients\Laravel\HttpClientsServiceProvider;

final class MyServiceProvider extends \Illuminate\Support\ServiceProvider {

    public function register() {
        parent::register();
        $this->app->singleton(HttpClientsServiceProvider::ServiceMainClient, static fn (): ClientInterface => new Client());
    }

} 
```

V tuto chvíli nám v aplikaci funguje DI na interface `Psr\Http\Client\ClientInterface`, jako třídu dostaneme `StrictPhp\HttpClients\Clients\MainHttp\MainHttpClient`. Třída `MainHttpClient` je vstupní pro všechny middlewares, takže si do ni můžete dát breakpoint a z ní začít krokovat. Nyní nevyužíváme žádných východ, které poskytuje knihovna.

Takže si zapneme middlwares, které potřebujeme v podobě registrace factory tříd. Na pořadí záleží, je jen na nás, které si povolíme. Použijeme konfiguraci pomocí pole, která je připravená. Konfigurační soubor se musí jmenovat [strictphp.http-clients.php](strictphp.http-clients.php).

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

Už jenom chybí si ukázat jak si nakonfigurujeme requesty na hosty. Každý middleware má k sobě konfigurační třídu, na příklad CacheResponseClient má CacheResponseConfig.

```php
use StrictPhp\HttpClients\Clients;

return [
    'config' => [
        'default' => [ // sdílená konfigurace pro všechny domény
            Clients\CacheResponse\CacheResponseConfig(604000, saveOnly: true)
        ],
        'example.com' => [
            Clients\CacheResponse\CacheResponseConfig(enabled: false), // vypne cache pro example.com
            Clients\Sleep\SleepConfig(enabled: false), // vypne sleep pro example.com
        ],
    ],
    //'factories' => [
    //    // list of ClientFactoryContract
    //],
    // 'storage' => 'http/',
];
```

## Vlastní middleware

Jak si napsat vlastní middleware je popsáno v [originálním návodu](https://github.com/strictphp/http-clients?tab=readme-ov-file#write-your-own-client).
