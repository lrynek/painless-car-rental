<?php
declare(strict_types=1);

namespace App\ValueObject;

final class Page
{
	private const MIN_VALUE = 1;
	private const MAX_VALUE = 9999;

	public function __construct(private int $value)
	{
		$this->validate();
	}

	public function value(): int
	{
		return $this->value;
	}

	public function equals(int|self $that): bool
	{
		if ($that instanceof self)
		{
			return $that->value === $this->value;
		}

		return $that === $this->value;
	}

	public function __toString(): string
	{
		return (string)$this->value;
	}

	private function validate(): void
	{
		if (self::MIN_VALUE > $this->value || self::MAX_VALUE < $this->value) {
			throw new \InvalidArgumentException;
		}
	}
}
