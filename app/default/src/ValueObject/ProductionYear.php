<?php
declare(strict_types=1);

namespace App\ValueObject;

final class ProductionYear
{
	/** @link https://www.daimler.com/company/tradition/company-history/1885-1886.html */
	private const MIN = 1886;
	private const MAX = 2022;

	public function __construct(
		private int $value
	)
	{
		$this->validate();
	}

	private function validate(): void
	{
		if ($this->value < self::MIN || $this->value > self::MAX) {
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
