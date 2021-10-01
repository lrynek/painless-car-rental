<?php
declare(strict_types=1);

namespace App\ValueObject;

interface CriteriaInterface
{
	public function required(): array;

	public function additional(): array;

	public function excluded(): array;
}
