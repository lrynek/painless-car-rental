<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Criteria;

interface Criterion
{
	public function definition(): array;
}
