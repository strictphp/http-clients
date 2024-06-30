<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Event\Events;

use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Clients\Event\Entities\HttpStateEntity;

final class SuccessRequestEvent extends AbstractCompleteRequestEvent
{
    public function __construct(
        HttpStateEntity $stateEntity,
        public readonly ResponseInterface $response,
    ) {
        parent::__construct($stateEntity);
    }
}
