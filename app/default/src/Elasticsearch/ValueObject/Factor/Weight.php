<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Factor;

final class Weight
{
	private const MIN_VALUE = 1;
	private const MAX_VALUE = 100;

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
