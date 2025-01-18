<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\CustomizeRequest;

use Psr\Http\Message\RequestInterface;
use StrictPhp\HttpClients\Entities\AbstractConfig;

final class CustomizeRequestConfig extends AbstractConfig
{
    /**
     * @readonly
     * @var callable(RequestInterface): RequestInterface
     */
    public $callback;

    public function __construct(?callable $callback = null)
    {
        $this->callback = $callback ?? static fn (RequestInterface $r): RequestInterface => $r;
    }
}
