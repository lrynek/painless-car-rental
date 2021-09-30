<?php
declare(strict_types=1);

namespace App\Enum;

abstract class AbstractEnum
{
	private string $value;

	/**
	 * @param mixed[] $arguments
	 *
	 * @return static
	 */
	final public static function __callStatic(string $staticMethodName, array $arguments): self
	{
		return new static(strtolower($staticMethodName));
	}

	/** @return static */
	final public static function fromString(string $enum): self
	{
		return new static($enum);
	}

	/** @return static[] */
	final public static function all(): array
	{
		return array_map(
			static function (string $value) {
				return new static($value);
			},
			self::allValues()
		);
	}

	/** @return string[] */
	final public static function allValues(): array
	{
		return array_values((new \ReflectionClass(static::class))->getConstants());
	}

	final public function value(): string
	{
		return $this->value;
	}

	final public function __toString(): string
	{
		return $this->value();
	}

	final public function valueEquals(string $value): bool
	{
		return $this->value() === $value;
	}

	final public function equals(self $that): bool
	{
		return $this->value === $that->value;
	}

	final public static function isValid(string $value): bool
	{
		return in_array($value, static::allValues(), true);
	}

	final private function __construct(string $value)
	{
		self::validate($value);

		$this->value = $value;
	}

	private static function validate(string $value): void
	{
		if (false === self::isValid($value))
		{
			throw new \InvalidArgumentException;
		}
	}
}
