<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Event\Events;


use StrictPhp\HttpClients\Clients\Event\Entities\HttpStateEntity;
use Throwable;

final class FailedRequestEvent extends AbstractCompleteRequestEvent
{
    public function __construct(HttpStateEntity $stateEntity, public readonly Throwable $exception)
    {
        parent::__construct($stateEntity);
    }
}
