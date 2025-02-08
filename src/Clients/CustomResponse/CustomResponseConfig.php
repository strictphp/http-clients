<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CustomResponse;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Entities\AbstractConfig;

final class CustomResponseConfig extends AbstractConfig
{
    /**
     * @param string|ResponseInterface|(callable(RequestInterface): ResponseInterface) $content - readonly
     */
    public function __construct(
        public readonly bool $enabled = false,
        public $content = 'success',
    ) {
    }
}
