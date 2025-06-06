<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Requests;

use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Clients\Event\Events\AbstractCompleteRequestEvent;
use StrictPhp\HttpClients\Clients\Event\Events\FailedRequestEvent;
use StrictPhp\HttpClients\Clients\Event\Events\SuccessRequestEvent;
use StrictPhp\HttpClients\Contracts\MakePathActionContract;
use StrictPhp\HttpClients\Contracts\StreamActionContract;
use StrictPhp\HttpClients\Filesystem\Contracts\FileFactoryContract;
use StrictPhp\HttpClients\Helpers\Headers;
use StrictPhp\HttpClients\Responses\SaveResponse;

/**
 * Use for event
 * @see FailedRequestEvent
 * @see SuccessRequestEvent
 */
final readonly class SaveForPhpstormRequest
{
    /**
     * @param positive-int|null $bufferSize
     */
    public function __construct(
        private FileFactoryContract $fileFactory,
        private MakePathActionContract $makePathAction,
        private SaveResponse $saveResponse,
        private StreamActionContract $streamAction,
        private ?int $bufferSize = null,
    ) {
    }

    public function save(
        AbstractCompleteRequestEvent $event,
        ?ResponseInterface $response = null,
        bool $serialized = false,
    ): void {
        $file = $this->fileFactory->create($this->makePathAction->execute($event, 'REQ.http'));
        $file->write('### Duration: ' . $event->duration . Headers::Eol);
        $file->write($event->request->getMethod() . ' ' . $event->request->getUri() . Headers::Eol);
        foreach (Headers::toIterable($event->request->getHeaders()) as $header) {
            $file->write($header . Headers::Eol);
        }
        $file->write(Headers::Eol);

        $this->streamAction->execute($event->request->getBody(), $file, $this->bufferSize);

        if ($response instanceof ResponseInterface) {
            $this->saveResponse->save($event, $response, $serialized);
        }
    }
}
