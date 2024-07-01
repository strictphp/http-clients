<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Entities;

use StrictPhp\HttpClients\Contracts\ConfigContract;

abstract class AbstractConfig implements ConfigContract
{
    public static function __set_state(array $an_array): object
    {
        return new static(...$an_array);
    }
}
