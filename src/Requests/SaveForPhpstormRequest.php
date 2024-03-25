<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Requests;

use Psr\Http\Message\ResponseInterface;
use SplFileObject;
use StrictPhp\HttpClients\Clients\Event\Events\AbstractCompleteRequestEvent;
use StrictPhp\HttpClients\Clients\Event\Events\BeforeRequestEvent;
use StrictPhp\HttpClients\Contracts\MakePathActionContract;
use StrictPhp\HttpClients\Helpers\Headers;
use StrictPhp\HttpClients\Responses\SaveResponse;

/**
 * Use for event
 * @see BeforeRequestEvent
 */
final class SaveForPhpstormRequest
{
    public function __construct(
        private readonly MakePathActionContract $makePathAction,
        private readonly SaveResponse $saveResponse,
        private readonly int $bufferSize = 8192
    ) {
    }

    public function save(AbstractCompleteRequestEvent $event, ?ResponseInterface $response = null): void
    {
        $file = new SplFileObject($this->makePathAction->execute($event, 'q.http'), 'w');
        $file->fwrite("### Duration: $event->duration" . Headers::Eol);
        $file->fwrite($event->request->getMethod() . ' ' . $event->request->getUri() . Headers::Eol);
        foreach (Headers::toIterable($event->request->getHeaders()) as $header) {
            $file->fwrite($header . Headers::Eol);
        }
        $file->fwrite(Headers::Eol);

        $stream = $event->request->getBody();
        $stream->rewind();
        while ($stream->eof() === false) {
            $file->fwrite($stream->read($this->bufferSize));
        }
        $stream->rewind();

        if ($response !== null) {
            $this->saveResponse->save($event, $response);
        }
    }
}
