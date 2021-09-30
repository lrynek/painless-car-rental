<?php
declare(strict_types=1);

namespace App\ValueObject;

final class ResultsPerPage
{
	private const MIN_VALUE = 1;
	private const MAX_VALUE = 10000;

	public function __construct(private int $value)
	{
		$this->validate();
	}

	public function value(): int
	{
		return $this->value;
	}

	private function validate(): void
	{
		if (self::MIN_VALUE > $this->value || self::MAX_VALUE < $this->value) {
			throw new \InvalidArgumentException;
		}
	}
}
