<?php
declare(strict_types=1);

namespace App\Elasticsearch\Hydrator;

use App\Elasticsearch\ValueObject\Response;
use App\Elasticsearch\ValueObject\Result;
use App\ValueObject\Cars;

final class CarsHydrator implements CarsHydratorInterface
{
	public function __construct(private CarHydratorInterface $carHydrator)
	{
	}

	public function hydrate(Response $response): Cars
	{
		$cars = [];

		/** @var Result $result */
		foreach ($response->results() as $result) {
			$cars[] = $this->carHydrator->hydrate($result);
		}

		return new Cars($response->total(), ...$cars);
	}
}
