<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Store;

use StrictPhp\HttpClients\Contracts\ConfigContract;

final class Config implements ConfigContract
{
	public function __construct(
	    public bool $enabled = false,
	    public bool $serialized = true,
	)
	{
	}

	public function initFromDefaultConfig(ConfigContract $object): void
	{
	}
}
