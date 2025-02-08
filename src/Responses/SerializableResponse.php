<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Responses;

use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use Serializable;
use StrictPhp\HttpClients\Filesystem\Interfaces\FileInterface;
use StrictPhp\HttpClients\Helpers\Stream;
use Stringable;

/**
 * @phpstan-type SerializeType array{class: class-string<ResponseInterface>, protocolVersion: string,
 *     headers: array<string>|array<string, array<string>>, code: int, reason: string, body: string, file: string}
 */
final class SerializableResponse implements Serializable, Stringable
{
    public function __construct(
        public readonly ResponseInterface $response,
        public readonly string $extension = '',
    ) {
    }

    public function __toString(): string
    {
        return $this->serialize();
    }

    /**
     * @param SerializeType $data
     */
    public function __unserialize(array $data): void
    {
        $response = new ($data['class']);

        foreach ($data['headers'] as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        if ($data['file'] === '') {
            $response = $response->withBody(Utils::streamFor($data['body']));
        }

        $this->extension = $data['file'] ?? ''; // @phpstan-ignore-line forward compatibility, file does not exist in previous version
        $this->response = $response
            ->withStatus($data['code'], $data['reason'])
            ->withProtocolVersion($data['protocolVersion']);
    }

    /**
     * @return SerializeType
     */
    public function __serialize(): array
    {
        return [
            'class' => $this->response::class,
            'protocolVersion' => $this->response->getProtocolVersion(),
            'headers' => $this->response->getHeaders(),
            'code' => $this->response->getStatusCode(),
            'reason' => $this->response->getReasonPhrase(),
            'body' => $this->extension === '' ? Stream::content($this->response->getBody()) : '',
            'file' => $this->extension,
        ];
    }

    public function setExternalBody(FileInterface $file): ResponseInterface
    {
        $stream = (new HttpFactory())->createStreamFromFile($file->getPathname());
        return $this->response->withBody($stream);
    }

    public function hasExternalBody(): bool
    {
        return $this->extension !== '';
    }

    public function unserialize(string $data): void
    {
        /** @var SerializeType $responseData */
        $responseData = unserialize($data);

        $this->__unserialize($responseData);
    }

    public function serialize(): string
    {
        return serialize($this->__serialize());
    }
}
