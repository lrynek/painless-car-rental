<?php
declare(strict_types=1);

namespace App\ValueObject;

interface SearchResponseInterface
{
	public function raw(): array;

	public function total(): int;

	public function maxScore(): float;

	public function results(): SearchResultsInterface;
}
