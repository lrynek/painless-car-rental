<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Factor;

use App\Elasticsearch\ValueObject\Query;

final class DodgePromoFactor extends WeightFactor
{
	private const FIELD_PRODUCER = 'producer';
	private const PRODUCER_DODGE = 'Dodge';

	public function definition(Query $query): array
	{
		return [
			'filter' => [
				'bool' => [
					'must' => [
						[
							'term' => [
								self::FIELD_PRODUCER => self::PRODUCER_DODGE,
							],
						],
					],
				],
			],
			'weight' => $this->weight->value(),
		];
	}
}
