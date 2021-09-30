<?php
declare(strict_types=1);

namespace App\ValueObject;

use App\Repository\Elasticsearch\ValueObject\Result;
use App\Repository\Elasticsearch\ValueObject\Results;

final class Cars implements \IteratorAggregate, \Countable
{
	private array $cars;

	public static function fromResults(Results $results): self
	{
		return new self(...$results->map(static function (Result $result) {
			return Car::fromResult($result);
		}));
	}

	public function getIterator(): \ArrayIterator
	{
		return new \ArrayIterator($this->cars);
	}

	public function count(): int
	{
		return count($this->cars);
	}

	private function __construct(Car ...$cars)
	{
		$this->cars = $cars;
	}
}
