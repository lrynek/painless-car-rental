<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Sorter;

use App\Elasticsearch\ValueObject\Factor\Factors;

interface FactorSorterInterface extends SorterInterface
{
	public function factors(): Factors;
}
