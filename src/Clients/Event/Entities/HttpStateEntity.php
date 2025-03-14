<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Event\Entities;

use Psr\Http\Message\RequestInterface;
use StrictPhp\HttpClients\Helpers\Time;

final class HttpStateEntity
{
    public readonly string $id;
    public readonly float $start;

    /**
     * readonly
     */
    public float $end = 0.0;

    /**
     * readonly
     */
    public float $duration = 0.0;

    public function __construct(
        public readonly RequestInterface $request,
    ) {
        $this->start = Time::seconds();
        $this->id = substr(md5($this->start . $this->request->getUri()), 0, 16);
    }

    public function finish(): self
    {
        if ($this->end === 0.0) {
            $this->end = Time::seconds();
            $this->duration = $this->end - $this->start;
        }

        return $this;
    }
}
