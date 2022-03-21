<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Factor;

use App\Elasticsearch\ValueObject\Query;

final class RawScoreFactor extends WeightFactor
{
	public function definition(Query $query): array
	{
		return [
			'script_score' => [
				'script' => '_score',
			],
			'weight' => $this->weight->value(),
		];
	}
}
