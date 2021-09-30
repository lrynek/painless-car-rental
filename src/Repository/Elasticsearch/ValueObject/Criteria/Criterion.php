<?php
declare(strict_types=1);

namespace App\Repository\Elasticsearch\ValueObject\Criteria;

interface Criterion
{
	public function definition(): array;
}
