<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Event\Exceptions;

use Psr\Http\Message\ResponseInterface;
use Throwable;

interface SuccessRequestEventExceptionInterface extends Throwable
{
    public function getResponse(): ResponseInterface;
}
