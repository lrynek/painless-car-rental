<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Criteria;

use App\ValueObject\Colors;

final class ColorsFilter implements Criterion
{
	private const FIELD = 'colors';

	public function __construct(private Colors $colors)
	{
	}

	public function definition(): array
	{
		return [
			'terms' => [
				self::FIELD => $this->colors->toArray(),
			],
		];
	}
}
