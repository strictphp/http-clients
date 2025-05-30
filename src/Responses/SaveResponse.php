<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Responses;

use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Clients\Event\Events\AbstractCompleteRequestEvent;
use StrictPhp\HttpClients\Clients\Event\Events\SuccessRequestEvent;
use StrictPhp\HttpClients\Contracts\FindExtensionFromHeadersActionContract;
use StrictPhp\HttpClients\Contracts\MakePathActionContract;
use StrictPhp\HttpClients\Contracts\StreamActionContract;
use StrictPhp\HttpClients\Filesystem\Contracts\FileFactoryContract;
use StrictPhp\HttpClients\Filesystem\Interfaces\FileInterface;
use StrictPhp\HttpClients\Helpers\Headers;

/**
 * Use for event
 * @see SuccessRequestEvent
 */
final readonly class SaveResponse
{
    /**
     * @param positive-int|null $bufferSize
     */
    public function __construct(
        private FileFactoryContract $fileFactory,
        private MakePathActionContract $makePathAction,
        private FindExtensionFromHeadersActionContract $findExtensionFromHeaders,
        private StreamActionContract $streamAction,
        private ?int $bufferSize = null,
    ) {
    }

    public function save(
        AbstractCompleteRequestEvent $event,
        ResponseInterface $response,
        bool $serialized = false,
    ): void {
        $this->headersAndBody($event, $event->duration, $response);

        if ($serialized) {
            $this->serialized($event, $response);
        }
    }

    public function headersAndBody(
        AbstractCompleteRequestEvent $event,
        float $duration,
        ResponseInterface $response,
    ): void {
        $this->headers($event, $duration, $response);
        $this->body($event, $response);
    }

    public function serialized(AbstractCompleteRequestEvent $event, ResponseInterface $response): void
    {
        $file = $this->createFile($event, $response, 'shttp');

        $file->write(serialize((new SerializableResponse($response))));
    }

    private function headers(AbstractCompleteRequestEvent $event, float $duration, ResponseInterface $response): void
    {
        $file = $this->createFile($event, $response, 'headers');
        $file->write(sprintf('### Duration: %s, code: ', $duration) . $response->getStatusCode() . Headers::Eol);
        foreach (Headers::toIterable($response->getHeaders()) as $header) {
            $file->write($header . Headers::Eol);
        }

        $file->write(Headers::Eol);
    }

    private function body(AbstractCompleteRequestEvent $event, ResponseInterface $response): void
    {
        $file = $this->createFile($event, $response, $this->findExtensionFromHeaders->execute($response));
        $this->streamAction->execute($response->getBody(), $file, $this->bufferSize);

        $file->write(Headers::Eol);
    }

    private function createFile(
        AbstractCompleteRequestEvent $event,
        ResponseInterface $response,
        string $extension,
    ): FileInterface {
        $code = $response->getStatusCode();
        return $this->fileFactory->create(
            $this->makePathAction->execute($event, sprintf('RES.%d.%s', $code, $extension)),
        );
    }
}
