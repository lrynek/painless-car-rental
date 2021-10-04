<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Factor;

use App\Elasticsearch\ValueObject\Query;

final class ColorRelevanceFactor extends WeightFactor
{
	private const MAXIMUM_COLORS_PER_DOCUMENT = 3;

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
			if (params.requiredColors.isEmpty()) {
    			return 0;
			}

			if (false === doc['colors'].containsAll(params.requiredColors)) {
    			return 0;
			}

            def colorExclusivnessFactor = (params.maximumColorsPerDocument + 1.0 - doc['colors'].size()) / params.maximumColorsPerDocument * 0.25;

            def requiredColorsCount = params.requiredColors.size();
			def positionsFactorSum = params.requiredColors
				.collect( requiredColor -> {
					if (false === doc['colors'].contains(requiredColor)) {	
						return 0;
					}

                    def position = params._source['colors'].indexOf(requiredColor) + 1.0;

					return (params.maximumColorsPerDocument + 1.0 - position) / params.maximumColorsPerDocument;
				})
				.sum() * 0.75;

            def colorPositionFactor = positionsFactorSum / requiredColorsCount;

			return (colorExclusivnessFactor + colorPositionFactor);
JS;
	}

	private function params(Query $query): array
	{
		return [
			'requiredColors' => $query->requiredColors(),
			'maximumColorsPerDocument' =>self::MAXIMUM_COLORS_PER_DOCUMENT,
		];
	}
}
