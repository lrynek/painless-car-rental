<?php
declare(strict_types=1);

namespace App\Elasticsearch\Hydrator;

use App\Elasticsearch\ValueObject\Result;
use App\ValueObject\Car;

interface CarHydratorInterface
{
	public function hydrate(Result $result): Car;
}
