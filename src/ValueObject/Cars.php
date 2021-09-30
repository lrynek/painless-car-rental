<?php
declare(strict_types=1);

namespace App\ValueObject;

use App\Repository\Elasticsearch\ValueObject\Response;
use App\Repository\Elasticsearch\ValueObject\Result;

final class Cars implements \IteratorAggregate, \Countable, PagesTotalAwareInterface
{
	private int $total;
	private array $cars;

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

	private function __construct(int $total, Car ...$cars)
	{
		$this->total = $total;
		$this->cars = $cars;
	}
}
