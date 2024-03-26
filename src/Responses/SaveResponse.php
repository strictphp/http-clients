<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Responses;

use Psr\Http\Message\ResponseInterface;
use SplFileObject;
use StrictPhp\HttpClients\Clients\Event\Events\AbstractCompleteRequestEvent;
use StrictPhp\HttpClients\Clients\Event\Events\SuccessRequestEvent;
use StrictPhp\HttpClients\Contracts\FindExtensionFromHeadersActionContract;
use StrictPhp\HttpClients\Contracts\MakePathActionContract;
use StrictPhp\HttpClients\Helpers\Headers;
use StrictPhp\HttpClients\Helpers\Stream;

/**
 * Use for event
 * @see SuccessRequestEvent
 */
final class SaveResponse
{
    public function __construct(
        private readonly MakePathActionContract $makePathAction,
        private readonly FindExtensionFromHeadersActionContract $findExtensionFromHeaders,
        private readonly int $bufferSize = 8192,
        private readonly ?bool $serialized = null,
    ) {
    }

    public function save(AbstractCompleteRequestEvent $event, ResponseInterface $response): void
    {
        $path = $this->makePathAction->execute($event) . 's.';

        if ($this->serialized === null || $this->serialized === false) {
            $this->headersAndBody($path, $event->duration, $response);
        }

        if ($this->serialized === null || $this->serialized === true) {
            $this->serialized($path, $response);
        }
    }

    public function headersAndBody(string $path, float $duration, ResponseInterface $response): void
    {
        $this->headers($path, $duration, $response);
        $this->body($path, $response);
    }


    public function serialized(string $path, ResponseInterface $response): void
    {
        $file = self::createSplFileObject($path . 'shttp');

        $file->fwrite((string) (new SerializableResponse($response)));
    }

    private function headers(string $path, float $duration, ResponseInterface $response): void
    {
        $file = self::createSplFileObject($path . 'headers');
        $file->fwrite("### Duration: $duration, code: " . $response->getStatusCode() . Headers::Eol);
        foreach (Headers::toIterable($response->getHeaders()) as $header) {
            $file->fwrite($header . Headers::Eol);
        }

        $file->fwrite(Headers::Eol);
    }


    private function body(string $path, ResponseInterface $response): void
    {
        $file = self::createSplFileObject($path . $this->findExtensionFromHeaders->execute($response));
        $stream = $response->getBody();
        Stream::fileWrite($stream, $file, $this->bufferSize);

        $file->fwrite(Headers::Eol);
    }

    private static function createSplFileObject(string $path): SplFileObject
    {
        return new SplFileObject($path, 'w');
    }
}
