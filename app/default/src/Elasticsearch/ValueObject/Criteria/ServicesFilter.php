<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Criteria;

use App\ValueObject\AdditionalServices;

final class ServicesFilter implements Criterion
{
	private const FIELD = 'service_ids';

	public function __construct(private AdditionalServices $services)
	{
	}

	public function definition(): array
	{
		return [
			'terms' => [
				self::FIELD => $this->services->getIds(),
			],
		];
	}
}
