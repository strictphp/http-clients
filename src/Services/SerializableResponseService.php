<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Services;

use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Contracts\FindExtensionFromHeadersActionContract;
use StrictPhp\HttpClients\Filesystem\Contracts\FileFactoryContract;
use StrictPhp\HttpClients\Helpers\Byte;
use StrictPhp\HttpClients\Responses\SerializableResponse;
use StrictPhp\HttpClients\Transformers\CacheKeyToFileInfoTransformer;

final class SerializableResponseService
{
    /**
     * @param int $limitByte - 0 = disable behavior
     */
    public function __construct(
        private readonly CacheKeyToFileInfoTransformer $cacheKeyToFileInfoTransformer,
        private readonly FileFactoryContract $fileFactory,
        private readonly FindExtensionFromHeadersActionContract $findExtensionFromHeadersAction,
        private readonly int $limitByte = Byte::Mega * 30,
    ) {
    }

    public function restore(string $key, mixed $result): mixed
    {
        if ($result instanceof SerializableResponse && $result->hasExternalBody()) {
            return $result->setExternalBody(
                $this->fileFactory->create(
                    $this->cacheKeyToFileInfoTransformer->transform($key, $result->extension),
                ),
            );
        }

        return $result;
    }

    public function store(string $key, ResponseInterface $response, ?int $limitByte = null): SerializableResponse
    {
        $limitByte ??= $this->limitByte;
        if ($limitByte > 0 && $response->getBody()->getSize() > $limitByte) {
            $extension = $this->findExtensionFromHeadersAction->execute($response);
            $file = $this->fileFactory->create($this->cacheKeyToFileInfoTransformer->transform($key, $extension));
            $file->write($response->getBody());
        } else {
            $extension = '';
        }

        return new SerializableResponse($response, $extension);
    }
}
