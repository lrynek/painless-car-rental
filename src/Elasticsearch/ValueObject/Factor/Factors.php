<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Factor;

final class Factors implements \IteratorAggregate
{
	private array $factors;

	public function __construct(FactorInterface ...$factors)
	{
		$this->factors = $factors;
	}

	public function getIterator(): \ArrayIterator
	{
		return new \ArrayIterator($this->factors);
	}
}
