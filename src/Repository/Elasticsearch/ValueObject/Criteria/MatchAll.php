<?php
declare(strict_types=1);

namespace App\Repository\Elasticsearch\ValueObject\Criteria;

final class MatchAll implements Criterion
{
	public function definition(): array
	{
		return [
			'match_all' => new \stdClass,
		];
	}
}
