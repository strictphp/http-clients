<?php

declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Event\Entities;

use Psr\Http\Message\RequestInterface;
use StrictPhp\HttpClients\Helpers\Time;

final class HttpStateEntity
{
    public readonly string $id;

    public readonly float $start;

    /**
     * @readonly
     */
    public float $end = 0.0;

    /**
     * @readonly
     */
    public float $duration = 0.0;


    public function __construct(public readonly RequestInterface $request)
    {
        $micro = Time::micro();
        $this->id = md5($micro . $this->request->getUri());
        $this->start = $micro;
    }

    public function finish(): self
    {
        $this->end = Time::micro();
        $this->duration = $this->end - $this->start;

        return $this;
    }

}
