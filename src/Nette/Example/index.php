<?php declare(strict_types=1);

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use StrictPhp\HttpClients\Nette\Example\Bootstrap;

require __DIR__ . '/../../../vendor/autoload.php';

$bootstrap = new Bootstrap();
$container = $bootstrap->bootWebApplication();

$requestFactory = $container->getByType(RequestFactoryInterface::class);
$client = $container->getByType(ClientInterface::class);

$request = $requestFactory->createRequest(
    'GET',
    'https://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/denni_kurz.txt',
);
$response = $client->sendRequest($request);

var_dump($response->getBody()->getContents());
