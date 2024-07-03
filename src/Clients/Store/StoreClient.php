<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Store;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Clients\Event\Entities\HttpStateEntity;
use StrictPhp\HttpClients\Clients\Event\Events\FailedRequestEvent;
use StrictPhp\HttpClients\Clients\Event\Events\SuccessRequestEvent;
use StrictPhp\HttpClients\Managers\ConfigManager;
use StrictPhp\HttpClients\Requests\SaveForPhpstormRequest;
use Throwable;

final class StoreClient implements ClientInterface
{
    public function __construct(
        private readonly SaveForPhpstormRequest $saveForPhpstormRequest,
        private readonly ConfigManager $configManager,
        private readonly ClientInterface $client,
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $config = $this->configManager->get(StoreConfig::class, $request->getUri()->getHost());

        if ($config->enabled === false) {
            return $this->client->sendRequest($request);
        }

        $state = new HttpStateEntity($request);
        try {
            $response = $this->client->sendRequest($request);
        } catch (Throwable $throwable) {
            if ($config->onFail) {
                $failed = new FailedRequestEvent($state->finish(), $throwable);
                $this->saveForPhpstormRequest->save($failed);
            }
            throw $throwable;
        }

        if ($config->onSuccess) {
            $success = new SuccessRequestEvent($state->finish(), $response);
            $this->saveForPhpstormRequest->save($success, $response, $config->serialized);
        }

        return $response;
    }
}
