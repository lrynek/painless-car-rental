<?php
declare(strict_types=1);

namespace App\ValueObject;

use App\Enum\Color;

final class Colors implements \IteratorAggregate, \Countable
{
	private array $colors;

	public function __construct(Color ...$colors)
	{
		$this->colors = $colors;
	}

	public static function fromArray(array $colors): self
	{
		return new self(...array_map(static function (string $color) {
			return Color::fromString($color);
		}, $colors));
	}

	public function getIterator(): \ArrayIterator
	{
		return new \ArrayIterator($this->colors);
	}

	public function count(): int
	{
		return count($this->colors);
	}

	public function __toString(): string
	{
		return implode(' | ', $this->colors);
	}
}
