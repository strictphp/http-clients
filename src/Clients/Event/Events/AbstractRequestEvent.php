<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Event\Events;

use Psr\Http\Message\RequestInterface;
use StrictPhp\HttpClients\Clients\Event\Entities\HttpStateEntity;

abstract class AbstractRequestEvent
{
    public readonly string $id;
    public readonly float $start;
    public readonly RequestInterface $request;

    public function __construct(HttpStateEntity $stateEntity)
    {
        $this->id = $stateEntity->id;
        $this->start = $stateEntity->start;
        $this->request = $stateEntity->request;
    }
}
