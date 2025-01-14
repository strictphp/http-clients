<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Mock;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Helpers\ResponseFactory;
use StrictPhp\HttpClients\Services\CacheRequestService;

final class MockClient implements ClientInterface
{
    /**
     * @param string|ResponseInterface|callable|CacheRequestService $content
     */
    public function __construct(
        private $content,
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return ResponseFactory::fromContent($this->content, $request);
    }
}
