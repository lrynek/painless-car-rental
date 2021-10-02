<?php
declare(strict_types=1);

namespace App\ValueObject;

use JetBrains\PhpStorm\Pure;

final class AdditionalService
{
	public function __construct(
		private Id   $id,
		private Name $name
	)
	{
	}

	public function id(): Id
	{
		return $this->id;
	}

	public function name(): Name
	{
		return $this->name;
	}

	#[Pure]
	public function __toString(): string
	{
		return $this->name->value();
	}
}
