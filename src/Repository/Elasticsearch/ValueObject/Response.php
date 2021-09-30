<?php
declare(strict_types=1);

namespace App\Repository\Elasticsearch\ValueObject;

use App\ValueObject\SearchResponseInterface;

final class Response implements SearchResponseInterface
{
	private int $total;
	private float $maxScore;
	private Results $results;

	public function __construct(private array $rawResponse)
	{
		$this->total = (int)($this->rawResponse['hits']['total'] ?? 0);
		$this->maxScore = (float)($this->rawResponse['hits']['max_score'] ?? 0.0);
		$this->results = Results::fromArray($this->rawResponse['hits']['hits'] ?? []);
	}

	public function raw(): array
	{
		return $this->rawResponse;
	}

	public function total(): int
	{
		return $this->total;
	}

	public function maxScore(): float
	{
		return $this->maxScore;
	}

	public function results(): Results
	{
		return $this->results;
	}
}
