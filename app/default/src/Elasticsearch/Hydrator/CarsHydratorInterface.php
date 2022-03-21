<?php
declare(strict_types=1);

namespace App\Elasticsearch\Hydrator;

use App\Elasticsearch\ValueObject\Response;
use App\ValueObject\Cars;

interface CarsHydratorInterface
{
	public function hydrate(Response $response): Cars;
}
