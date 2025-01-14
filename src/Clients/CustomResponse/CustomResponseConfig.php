<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CustomResponse;

use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Contracts\ConfigInterface;
use StrictPhp\HttpClients\Entities\AbstractConfig;
use StrictPhp\HttpClients\Services\CacheRequestService;

final class CustomResponseConfig extends AbstractConfig
{
    /**
     * @param string|ResponseInterface|callable|CacheRequestService $content - readonly
     */
    public function __construct(
        public readonly bool $enabled = true,
        public $content = 'success',
    )
    {
    }

    public function initFromDefaultConfig(ConfigInterface $object): void
    {
    }
}
