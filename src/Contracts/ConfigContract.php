<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Contracts;

/**
 * parameters in constructor are not require
 */
interface ConfigContract
{
    public function __construct();

    /**
     * @param static $object
     */
    public function initFromDefaultConfig(ConfigContract $object): void;
}
