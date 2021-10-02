<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Factor;

use App\Elasticsearch\ValueObject\Query;

final class BlueColorRelevanceFactor extends WeightFactor
{
	public function definition(Query $query): array
	{
		return [
			'script_score' => [
				'script' => [
					'source' => $this->script(),
					'params' => $this->params($query),
				]
			],
			'weight' => $this->weight->value(),
		];
	}

	private function script(): string
	{
		return <<<JS
			if (f)
			if (false === doc['colors'].contains('blue')) {
    			return 0;
			}
			
			def position = doc['colors'].indexOf('blue');
			
			return (4 - position) / 3;
JS;
	}

	private function params(Query $query): array
	{
		return [
			'filtered_color' => $query->filteredColor()
		];
	}
}
