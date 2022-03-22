<?php
declare(strict_types=1);

namespace App\ValueObject;

use App\Elasticsearch\ValueObject\Response;
use App\Elasticsearch\ValueObject\Result;

final class Cars implements \IteratorAggregate, \Countable, PagesTotalAwareInterface
{
	private int $total;
	private array $cars;

	public function __construct(int $total, Car ...$cars)
	{
		$this->total = $total;
		$this->cars = $cars;
	}

	public static function fromResponse(Response $response): self
	{
		$cars = $response->results()->map(static function (Result $result) {
			return Car::fromResult($result);
		});

		return new self(
			$response->total(),
			...$cars
		);
	}

	public function getIterator(): \ArrayIterator
	{
		return new \ArrayIterator($this->cars);
	}

	public function count(): int
	{
		return count($this->cars);
	}

	public function total(): int
	{
		return $this->total;
	}

	public function empty(): bool
	{
		return 0 === $this->count();
	}
}
