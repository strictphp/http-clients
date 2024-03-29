<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Responses;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use Serializable;
use StrictPhp\HttpClients\Helpers\Stream;
use Stringable;

/**
 * @phpstan-type SerializeType array{class: class-string<ResponseInterface>, protocolVersion: string,
 *     headers: array<string>|array<string, array<string>>, code: int, reason: string, body: string}
 */
final class SerializableResponse implements Serializable, Stringable
{
    public function __construct(
        public readonly ResponseInterface $response,
    ) {
    }

    public function unserialize(string $data): void
    {
        /** @var SerializeType $responseData */
        $responseData = unserialize($data);

        $this->__unserialize($responseData);
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

        $this->response = $response->withStatus($data['code'], $data['reason'])
            ->withProtocolVersion($data['protocolVersion'])
            ->withBody(Utils::streamFor($data['body']));
    }

    public function __toString(): string
    {
        return (string) $this->serialize();
    }

    public function serialize(): ?string
    {
        return serialize($this->__serialize());
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
            'body' => Stream::content($this->response->getBody()),
        ];
    }
}
