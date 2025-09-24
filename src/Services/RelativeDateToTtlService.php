<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Services;

use Psr\Clock\ClockInterface;
use StrictPhp\HttpClients\Exceptions\LogicException;
use StrictPhp\HttpClients\Helpers\Time;

final readonly class RelativeDateToTtlService
{
    public function __construct(
        private ClockInterface $clock,
    ) {
    }

    public function midnight(): int
    {
        return $this->extend('tomorrow');
    }

    /**
     * @example $time = 2 days, 1 hour, 30 minutes
     */
    public function extend(string $time): int
    {
        $now = $this->clock->now();
        $then = $now->modify($time);

        if ($then <= $now) {
            $interval = $now->diff($then);
            if ($interval->d >= 1 || $interval->m >= 1 || $interval->y >= 1) {
                throw new LogicException('The relative time must be in the future.');
            }
            $then = $then->modify('+1 day');
        }

        return $then->getTimestamp() - $now->getTimestamp();
    }

    /**
     * @example $time = 13:30, 8:00, 23:59:59
     */
    public function time(string $time): int
    {
        if (Time::matchTime($time) === false) {
            throw new LogicException('Invalid time format. Use "H:i" or "H:i:s".');
        }

        [$hour, $minutes, $seconds] = explode(':', $time . ':00');

        $now = $this->clock->now();
        $then = $now->setTime((int) $hour, (int) $minutes, (int) $seconds);

        if ($then <= $now) {
            $then = $then->modify('+1 day');
        }

        return $then->getTimestamp() - $now->getTimestamp();
    }
}
