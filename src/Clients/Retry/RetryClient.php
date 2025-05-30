<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Retry;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Clients\Sleep\SleepClient;
use StrictPhp\HttpClients\Exceptions\LogicException;
use StrictPhp\HttpClients\Managers\ConfigManager;
use Throwable;

/**
 * Recommended to use before SleepClient
 * @see SleepClient
 */
final readonly class RetryClient implements ClientInterface
{
    public function __construct(
        private ClientInterface $client,
        private ConfigManager $configManager,
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $config = $this->configManager->get(RetryConfig::class, $request->getUri()->getHost());
        $max = $config->tries - 1;
        for ($i = 0; $i < $config->tries; ++$i) {
            try {
                return $this->client->sendRequest($request);
            } catch (Throwable $e) {
                if (($config->isMyException)($e) === false || $i === $max) {
                    throw $e;
                }
                // intentionally try next
            }
        }

        throw new LogicException('Number of tries invalid.');
    }
}
