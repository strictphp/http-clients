<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Contracts;

/**
 * parameters in constructor are not require
 */
interface ConfigInterface
{
    public function __construct();

    /**
     * @param array<string, mixed> $anArray
     * @return static
     */
    public static function __set_state(array $anArray): object;

    /**
     * @param static $object
     */
    public function initFromDefaultConfig(self $object): void;
}
