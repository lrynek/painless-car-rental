<?php
declare(strict_types=1);

namespace App\Repository\Elasticsearch\ValueObject;

final class Results implements \IteratorAggregate, \Countable
{
	private array $results;

	public function __construct(Result ...$results)
	{
		$this->results = $results;
	}

	public static function fromArray(array $array): self
	{
		return new self(...array_map(static function (array $result) { return Result::fromArray($result);}, $array));
	}

	public function getIterator(): \ArrayIterator
	{
		return new \ArrayIterator($this->results);
	}

	public function count(): int
	{
		return count($this->results);
	}

	public function map(callable $callback): array
	{
		return array_map($callback, $this->results);
	}
}
