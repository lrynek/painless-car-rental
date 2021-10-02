<?php
declare(strict_types=1);

namespace App\ValueObject;

use App\Enum\Color;

final class AdditionalServices implements \IteratorAggregate, \Countable
{
	private array $services;

	public function __construct(AdditionalService ...$services)
	{
		$this->services = $services;
	}

	public function isEmpty(): bool
	{
		return empty($this->services);
	}

	public function getIterator(): \ArrayIterator
	{
		return new \ArrayIterator($this->services);
	}

	public function count(): int
	{
		return count($this->services);
	}
}
