<?php

declare(strict_types = 1);

namespace StrictPhp\HttpClients\Exceptions;

use Throwable;

abstract class RuntimeException extends \RuntimeException
{
    public function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, $previous instanceof Throwable ? $previous->getCode() : 0, $previous);
    }
}
