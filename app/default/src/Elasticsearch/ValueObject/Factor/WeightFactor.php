<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Factor;

abstract class WeightFactor implements FactorInterface
{
	public function __construct(protected Weight $weight)
	{
	}

	public function name(): string
	{
		return static::class;
	}

	protected function weight(): Weight
	{
		return $this->weight;
	}
}
