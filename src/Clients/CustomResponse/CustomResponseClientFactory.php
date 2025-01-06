<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CustomResponse;

use Closure;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Contracts\ClientFactoryContract;
use StrictPhp\HttpClients\Services\CacheRequestService;

/**
 * Use as last middleware
 */
final class CustomResponseClientFactory implements ClientFactoryContract
{
    public function __construct(
        private readonly string|ResponseInterface|Closure|CacheRequestService $content,
    )
    {
    }

    public function create(ClientInterface $client): ClientInterface
    {
        return new CustomResponseClient($this->content);
    }
}
