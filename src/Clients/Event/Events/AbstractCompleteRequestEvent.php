<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Event\Events;

use StrictPhp\HttpClients\Clients\Event\Entities\HttpStateEntity;

abstract class AbstractCompleteRequestEvent extends AbstractRequestEvent
{
    public readonly float $end;
    public readonly float $duration;

    public function __construct(HttpStateEntity $stateEntity)
    {
        parent::__construct($stateEntity);
        $this->end = $stateEntity->end;
        $this->duration = $stateEntity->duration;
    }
}
