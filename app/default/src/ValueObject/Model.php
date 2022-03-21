<?php
declare(strict_types=1);

namespace App\ValueObject;

final class Model
{
	public function __construct(
		private string $value
	)
	{
		$this->validate();
	}

	private function validate(): void
	{
		if (empty($this->value)) {
			throw new \InvalidArgumentException;
		}
	}

	public function value(): string
	{
		return $this->value;
	}

	public function __toString(): string
	{
		return $this->value;
	}
}
