<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Sorter;

use App\Elasticsearch\ValueObject\Factor\ColorRelevanceFactor;
use App\Elasticsearch\ValueObject\Factor\DodgePromoFactor;
use App\Elasticsearch\ValueObject\Factor\Factors;
use App\Elasticsearch\ValueObject\Factor\RawScoreFactor;
use App\Elasticsearch\ValueObject\Factor\Weight;

final class RecommendedSorter implements FactorSorterInterface
{
	public function __construct(private ?Factors $factors = null)
	{
		$this->factors ??= new Factors(
			new RawScoreFactor(new Weight(1)),
			new DodgePromoFactor(new Weight(100)),
			new ColorRelevanceFactor(new Weight(50)),
		);
	}

	public function factors(): Factors
	{
		return $this->factors;
	}

	public function definition(): array
	{
		return [
			'_score' => self::DESCENDING
		];
	}
}
