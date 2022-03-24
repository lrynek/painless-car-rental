<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Sorter;

interface SorterInterface
{
	public const ASCENDING = 'asc';
	public const DESCENDING = 'desc';

	public function definition(): array;
}
