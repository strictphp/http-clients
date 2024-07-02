<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CustomResponse;

use Closure;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Services\CacheRequestService;
use StrictPhp\HttpClients\Services\LoadCustomFileService;

class CustomResponseClient implements ClientInterface
{
    /**
     * @param string|ResponseInterface|CacheRequestService|Closure(): ResponseInterface $content
     */
    public function __construct(
        private readonly string|ResponseInterface|Closure|CacheRequestService $content,
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if ($this->content instanceof Closure) {
            return ($this->content)();
        } elseif (is_string($this->content) && is_file($this->content)) {
            $cache = new CacheRequestService(new LoadCustomFileService($this->content));
            $body = $cache->restore('');
        } elseif ($this->content instanceof CacheRequestService){
            $body = $this->content->restore('');
        } else {
            $body = $this->content;
        }

        return $body instanceof ResponseInterface
            ? $body
            : new Response(body: (string) $body);
    }
}
