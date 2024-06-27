<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CustomResponse;

use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Filesystem\Filesystem;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Responses\SerializableResponse;

class CustomResponseClient implements ClientInterface
{
    public function __construct(
        private readonly string $content,
        private readonly ?Filesystem $filesystem = null,
    )
    {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if ($this->filesystem instanceof Filesystem && $this->filesystem->exists($this->content)) {
            $body = (string) $this->filesystem->get($this->content);
        } elseif (is_file($this->content)) {
            $body = (string) file_get_contents($this->content);
        } else {
            $body = $this->content;
        }

        if ($body !== '') {
            $response = @unserialize($body);
            if ($response instanceof SerializableResponse) {
                return $response->response;
            }
        }

        return new Response(body: $body);
    }
}
