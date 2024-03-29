<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Contracts;

/**
 * keep constructor with default parameters
 */
interface ConfigContract
{
    /**
     * @param static $object
     */
    public function initByDefault(ConfigContract $object): void;
}
