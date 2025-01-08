<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Tests\Nette\DI;

use Psr\EventDispatcher\EventDispatcherInterface;
use stdClass;

final class EventDispatcherMock implements EventDispatcherInterface
{
    public function dispatch(object $event): object
    {
        return new stdClass();
    }
}
