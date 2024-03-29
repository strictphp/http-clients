<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Responses;

use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Clients\Event\Entities\FileInfoEntity;
use StrictPhp\HttpClients\Clients\Event\Events\AbstractCompleteRequestEvent;
use StrictPhp\HttpClients\Clients\Event\Events\SuccessRequestEvent;
use StrictPhp\HttpClients\Contracts\FindExtensionFromHeadersActionContract;
use StrictPhp\HttpClients\Contracts\MakePathActionContract;
use StrictPhp\HttpClients\Contracts\StreamActionContract;
use StrictPhp\HttpClients\Filesystem\Contracts\FileFactoryContract;
use StrictPhp\HttpClients\Helpers\Headers;

/**
 * Use for event
 * @see SuccessRequestEvent
 */
final class SaveResponse
{
    public function __construct(
        private readonly FileFactoryContract $fileFactory,
        private readonly MakePathActionContract $makePathAction,
        private readonly FindExtensionFromHeadersActionContract $findExtensionFromHeaders,
        private readonly StreamActionContract $streamAction,
        private readonly int $bufferSize = 8192,
        private readonly ?bool $serialized = null,
    ) {
    }

    public function save(AbstractCompleteRequestEvent $event, ResponseInterface $response, ?bool $serialized = null): void
    {
        $serialized ??= $this->serialized;

        $fileInfo = $this->makePathAction->execute($event, 's.');

        if ($serialized === null || $serialized === false) {
            $this->headersAndBody($fileInfo, $event->duration, $response);
        }

        if ($serialized === null || $serialized === true) {
            $this->serialized($fileInfo, $response);
        }
    }

    public function headersAndBody(FileInfoEntity $fileInfo, float $duration, ResponseInterface $response): void
    {
        $this->headers($fileInfo, $duration, $response);
        $this->body($fileInfo, $response);
    }

    private function headers(FileInfoEntity $fileInfo, float $duration, ResponseInterface $response): void
    {
        $file = $this->fileFactory->create($fileInfo, 'headers');
        $file->write("### Duration: $duration, code: " . $response->getStatusCode() . Headers::Eol);
        foreach (Headers::toIterable($response->getHeaders()) as $header) {
            $file->write($header . Headers::Eol);
        }

        $file->write(Headers::Eol);
    }

    private function body(FileInfoEntity $fileInfo, ResponseInterface $response): void
    {
        $file = $this->fileFactory->create($fileInfo, $this->findExtensionFromHeaders->execute($response));
        $this->streamAction->execute($response->getBody(), $file, $this->bufferSize);

        $file->write(Headers::Eol);
    }

    public function serialized(FileInfoEntity $fileInfo, ResponseInterface $response): void
    {
        $file = $this->fileFactory->create($fileInfo, 'shttp');

        $file->write((string) (new SerializableResponse($response)));
    }
}
