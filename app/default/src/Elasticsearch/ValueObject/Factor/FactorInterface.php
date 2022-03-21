<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Factor;

use App\Elasticsearch\ValueObject\Query;

interface FactorInterface
{
	public function name(): string;

	public function definition(Query $query): array;
}
