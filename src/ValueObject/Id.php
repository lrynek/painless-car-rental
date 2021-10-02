<?php
declare(strict_types=1);

namespace App\ValueObject;

final class Id
{
	public function __construct(
		private int $value
	)
	{
		$this->validate();
	}

	private function validate(): void
	{
		if (1 > $this->value) {
			throw new \InvalidArgumentException;
		}
	}

	public function value(): int
	{
		return $this->value;
	}

	public function __toString(): string
	{
		return (string)$this->value;
	}
}
