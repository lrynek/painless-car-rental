<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject;

final class Result
{
	private const ID = '_id';
	private const SCORE = '_score';
	private const SOURCE = '_source';

	private string $id;
	private float $score;
	private array $source;

	public static function fromArray(array $array): self
	{
		return new self($array);
	}

	public function raw(): array
	{
		return $this->rawResult;
	}

	public function id(): string
	{
		return $this->id;
	}

	public function score(): float
	{
		return $this->score;
	}

	public function source(): array
	{
		return $this->source;
	}

	private function __construct(private array $rawResult)
	{
		$this->validate();
		$this->id = (string)($this->rawResult[self::ID] ?? '');
		$this->score = (float)($this->rawResult[self::SCORE] ?? 0.0);
		$this->source = $this->rawResult[self::SOURCE] ?? [];
	}

	private function validate(): void
	{
		if (false === array_key_exists(self::ID, $this->rawResult)) {
			throw new \InvalidArgumentException;
		}

		if (false === array_key_exists(self::SCORE, $this->rawResult)) {
			throw new \InvalidArgumentException;
		}

		if (false === array_key_exists(self::SOURCE, $this->rawResult)) {
			throw new \InvalidArgumentException;
		}
	}
}
